<?php

declare(strict_types=1);

namespace Bunches\Postcode;

use Bunches\Postcode\Exception\InvalidPostcode;

class Postcode
{
    /**
     * Permitted letters depend upon their position in the postcode.
     */
    private const ALPHA1 = '[abcdefghijklmnoprstuwyz]';
    private const ALPHA2 = '[abcdefghklmnopqrstuvwxy]';
    private const ALPHA3 = '[abcdefghjkstuw]';
    private const ALPHA4 = '[abehmnprvwxy]';
    private const ALPHA5 = '[abdefghjlnpqrstuwxyz]';

    /**
     * FORMAT EXAMPLE:
     * AN NAA - M1 1AA
     * ANN NAA - M60 1NW
     * AAN NAA - CR2 6XH
     * AANN NAA - DN55 1PT
     * ANA NAA - W1A 1HQ
     * AANA NAA - EC1A 1BB.
     */
    private string $PO;
    private string $district;
    private string $sector = '';
    private string $unit = '';

    public function __construct(string $postcode = '')
    {
        $sanitisedPostcode = $this->sanitisePostcode($postcode);

        if ($this->validate($sanitisedPostcode)) {
            $this->setPostcode($sanitisedPostcode);
        } else {
            throw new InvalidPostcode('Invalid postcode: ' . $postcode);
        }
    }

    public function getPo(): string
    {
        return $this->PO;
    }

    public function getDistrict(): string
    {
        return $this->district;
    }

    public function getSector(): string
    {
        return $this->sector;
    }

    public function getUnit(): string
    {
        return $this->unit;
    }

    public function getPostcode(): string
    {
        return $this->PO . $this->district . $this->sector . $this->unit;
    }

    public function getPostCodeWithSpace(): string
    {
        return $this->PO . $this->district . ' ' . $this->sector . $this->unit;
    }

    private function sanitisePostcode(string $postcode): string
    {
        $trimmedPostCode = \trim($postcode);

        return \preg_replace('!\s+!', ' ', $trimmedPostCode);
    }

    private function setPostcode(string $postcode): void
    {
        $splitPostCode = $this->splitPostcode($postcode);
        $this->setSecondHalfOfPostcode($splitPostCode);
        $this->setFirstHalfOfPostcode($splitPostCode);
    }

    private function validate(string $postcode): bool
    {
        $valid = false;
        foreach ($this->buildPostcodeExpressions() as $regexp) {
            if (\preg_match('/' . $regexp . '/i', \strtolower($postcode), $matches)) {
                $valid = true;
                break;
            }
        }

        return $valid;
    }

    private function splitPostcode(string $postcode): array
    {
        $splitPostCode = [];
        if (\preg_match('/\s/', $postcode)) {
            $splitPostCode = \explode(' ', $postcode);
        } elseif (\strlen($postcode) > 4) {
            $splitPostCode[0] = \substr($postcode, 0, -3);
            $splitPostCode[1] = \substr($postcode, -3);
        } else {
            $splitPostCode[0] = $postcode;
        }

        return $splitPostCode;
    }

    private function setSecondHalfOfPostcode(array $splitPostCode): void
    {
        if (\array_key_exists(1, $splitPostCode)) {
            $this->sector = $splitPostCode[1][0];
            $this->unit = \substr($splitPostCode[1], -2);
        }
    }

    private function setFirstHalfOfPostcode(array $splitPostCode): void
    {
        if (\ctype_digit(\substr($splitPostCode[0], 1, 1))) {
            $this->PO = \substr($splitPostCode[0], 0, 1);
            $length = (\strlen($splitPostCode[0]) - 1) * -1;
        } else {
            $this->PO = \substr($splitPostCode[0], 0, 2);
            $length = (\strlen($splitPostCode[0]) - 2) * -1;
        }
        $this->district = \substr($splitPostCode[0], $length);
    }

    private function buildPostcodeExpressions(): array
    {
        $postcodeExpression = [];
        $postcodeExpression[0] = '^(' . self::ALPHA1 . '{1}' . self::ALPHA2 . '{0,1}[0-9]{1,2})([[:space:]]{0,})([0-9]{1}' . self::ALPHA5 . '{2})?$'; /* AN NAA, ANN NAA, AAN NAA, and AANN NAA with a space or AN, ANN, AAN, AANN with no whitespace */
        $postcodeExpression[1] = '^(' . self::ALPHA1 . '{1}[0-9]{1}' . self::ALPHA3 . '{1})([[:space:]]{0,})([0-9]{1}' . self::ALPHA5 . '{2})?$'; /* ANA NAA or ANA with no whitespace */
        $postcodeExpression[2] = '^(' . self::ALPHA1 . '{1}' . self::ALPHA2 . '[0-9]{1}' . self::ALPHA4 . ')([[:space:]]{0,})([0-9]{1}' . self::ALPHA5 . '{2})?$'; /* AANA NAA or AANA With no whitespace */
        $postcodeExpression[3] = '^(gir)([[:space:]]{0,})?(0aa)?$'; /* GIR 0AA or just GIR */
        $postcodeExpression[4] = '^(bfpo)([[:space:]]{0,})([0-9]{1,4})$'; /* BFPO */
        $postcodeExpression[5] = '^(bfpo)([[:space:]]{0,})(c\/o([[:space:]]{0,})[0-9]{1,3})$'; /* c/o BFPO numbers */
        $postcodeExpression[6] = '^([a-z]{4})([[:space:]]{0,})(1zz)$'; /* Overseas Territories */
        $postcodeExpression[7] = '^(ai\-2640)$'; /* Anquilla */

        return $postcodeExpression;
    }
}
