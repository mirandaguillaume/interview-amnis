<?php

namespace App\Exceptions;

use App\Enums\Currency;

class NoAccountForCurrency extends InterviewProjectException
{
    public function __construct(Currency $currency, $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct("No account for currency $currency->value.", $code, $previous);
    }
}
