<?php

namespace App\Exceptions;

use App\Enums\TransactionTypeEnum;

class WrongTransactionType extends TransactionExecutionException
{
    public function __construct(
        TransactionTypeEnum $expected,
        TransactionTypeEnum $wrong,
        int $code = 0,
        ?\Throwable $previous = null,
    ){
        parent::__construct(
            "Transaction type should be $expected->value, received $wrong->value.",
            $code,
            $previous,
        );
    }
}
