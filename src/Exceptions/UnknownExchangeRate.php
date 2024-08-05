<?php

namespace App\Exceptions;

use App\Enums\Currency;

class UnknownExchangeRate extends \Exception
{
    public function __construct(
        Currency $from,
        Currency $to,
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct(
            "Unknown exchange rate from $from->value to $to->value",
            $code,
            $previous,
        );
    }
}
