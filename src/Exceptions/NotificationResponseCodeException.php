<?php

namespace Orumad\LaravelRedsys\Exceptions;

use Exception;

class NotificationResponseCodeException extends Exception
{
    public static function invalidResponseCode(): self
    {
        return new static('The response code for the operation is not valid.');
    }
}
