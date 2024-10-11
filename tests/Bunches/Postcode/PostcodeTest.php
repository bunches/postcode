<?php

declare(strict_types=1);

namespace Bunches\Tests\Postcode;

use Bunches\Postcode\Exception\InvalidPostcode;
use Bunches\Postcode\Postcode;
use PHPUnit\Framework\TestCase;

final class PostcodeTest extends TestCase
{
    public function testThrowsAnExceptionWhenGivenAnInvalidPostcode(): void
    {
        $this->expectException(InvalidPostcode::class);
        new Postcode('invalid postcode');
    }

    public function testItBreaksUpAndSetsValidPostcode(): void
    {
        $postcode = new Postcode('NG15 0DQ');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals(0, $postcode->getSector());
        $this->assertEquals('DQ', $postcode->getUnit());
        $this->assertEquals('NG150DQ', $postcode->getPostcode());
        $this->assertEquals('NG15 0DQ', $postcode->getPostCodeWithSpace());
    }

    public function testItBreaksUpAndSetsValidPostcodeWithNoSpace(): void
    {
        $postcode = new Postcode('NG150DQ');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals(0, $postcode->getSector());
        $this->assertEquals('DQ', $postcode->getUnit());
        $this->assertEquals('NG150DQ', $postcode->getPostcode());
        $this->assertEquals('NG15 0DQ', $postcode->getPostCodeWithSpace());
    }

    public function testItBreaksUpAndSetsValidPostcodeWithSpaceEitherSide(): void
    {
        $postcode = new Postcode(' NG150DQ ');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals(0, $postcode->getSector());
        $this->assertEquals('DQ', $postcode->getUnit());
        $this->assertEquals('NG150DQ', $postcode->getPostcode());
        $this->assertEquals('NG15 0DQ', $postcode->getPostCodeWithSpace());
    }

    public function testItBreaksUpAndSetsValidPostcodeWithMultipleSpaces(): void
    {
        $postcode = new Postcode('NG150DQ  ');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals(0, $postcode->getSector());
        $this->assertEquals('DQ', $postcode->getUnit());
        $this->assertEquals('NG150DQ', $postcode->getPostcode());
        $this->assertEquals('NG15 0DQ', $postcode->getPostCodeWithSpace());
    }

    public function testItBreaksUpAndSetsValidPostcodeWithMultipleSpacesInTheMiddle(): void
    {
        $postcode = new Postcode('NG15  0DQ');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals(0, $postcode->getSector());
        $this->assertEquals('DQ', $postcode->getUnit());
        $this->assertEquals('NG150DQ', $postcode->getPostcode());
        $this->assertEquals('NG15 0DQ', $postcode->getPostCodeWithSpace());
    }

    public function testItBreaksUpPartialPostcode(): void
    {
        $postcode = new Postcode('NG15');

        $this->assertEquals('NG', $postcode->getPo());
        $this->assertEquals('15', $postcode->getDistrict());
        $this->assertEquals('NG15', $postcode->getPostcode());
        $this->assertEquals('NG15 ', $postcode->getPostCodeWithSpace());
    }

    public function testPostcodesWithThreeCharacterFirstHalf(): void
    {
        $postcode = new Postcode('W1A');

        $this->assertEquals('W', $postcode->getPo());
        $this->assertEquals('1A', $postcode->getDistrict());
        $this->assertEquals('W1A', $postcode->getPostcode());
        $this->assertEquals('W1A ', $postcode->getPostCodeWithSpace());
    }
}
