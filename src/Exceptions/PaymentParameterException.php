<?php

namespace Orumad\LaravelRedsys\Exceptions;

use Exception;

class PaymentParameterException extends Exception
{
    public static function invalidOrderFormat(): self
    {
        return new static('The order number format is not valid.');
    }

    public static function invalidMerchantCode(): self
    {
        return new static('The Merchant Code is not valid. Must be 9-digits number.');
    }

    public static function invalidAmount(): self
    {
        return new static('The amount format is not valid.');
    }

    public static function invalidCurrency(): self
    {
        return new static('The currency code format is not valid.');
    }

    public static function invalidTerminal(): self
    {
        return new static('The terminal number is not valid.');
    }

    public static function invalidTransactionType(): self
    {
        return new static('The transaction type is not valid.');
    }
}
