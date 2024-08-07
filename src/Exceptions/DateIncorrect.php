<?php

namespace App\Exceptions;

class DateIncorrect extends TransactionExecutionException
{
    public function __construct(\DateTimeImmutable $givenDate, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct(
            "Payout transaction date can be only on the current date, given date is {$givenDate->format('Y-m-d')}.",
            $code,
            $previous,
        );
    }
}
