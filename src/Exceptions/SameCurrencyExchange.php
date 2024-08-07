<?php

namespace App\Exceptions;

class SameCurrencyExchange extends ExchangeException
{
    public function __construct(int $code = 0, \Throwable $previous = null)
    {
        parent::__construct("Can't create an exchange on the same currency.", $code, $previous);
    }
}
