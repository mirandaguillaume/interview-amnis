<?php

namespace App\Exceptions;

class AlreadyExecutedTransaction extends TransactionExecutionException
{
    public function __construct(int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct('Transaction is already executed', $code, $previous);
    }
}
