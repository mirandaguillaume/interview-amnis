<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\AlreadyExecutedTransaction;
use App\Exceptions\TransactionExecutionException;
use App\Exceptions\WrongTransactionType;

class PayinManager
{
    public function __construct(private readonly AccountManager $accountManager)
    {
    }

    public function execute(Transaction $transaction): void
    {
        $transaction->getType() !== TransactionTypeEnum::PAYIN ?:
            throw new WrongTransactionType(
                TransactionTypeEnum::PAYIN,
                $transaction->getType(),
            );

        $transaction->isExecuted() ?: throw new AlreadyExecutedTransaction();

        $transaction->setExecuted(true);

        $this->accountManager->increaseBalance(
            $transaction->getAccount(),
            $transaction->getAmount(),
        );
    }
}
