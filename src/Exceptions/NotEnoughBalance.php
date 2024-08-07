<?php

namespace App\Exceptions;

class NotEnoughBalance extends InterviewProjectException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('You do not have enough money for a payout', $code, $previous);
    }
}
