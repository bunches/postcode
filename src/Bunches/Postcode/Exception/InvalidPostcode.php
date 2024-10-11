<?php

declare(strict_types=1);

namespace Bunches\Postcode\Exception;

final class InvalidPostcode extends \Exception
{
    protected $message = 'Invalid postcode.';
}
