<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\TransactionExecutionException;

class PayinManager
{
    public function __construct(private readonly AccountManager $accountManager)
    {
    }

    public function execute(Transaction $transaction): void
    {
        if ($transaction->getType() !== TransactionTypeEnum::PAYIN) {
            throw new TransactionExecutionException('Transaction type is not payin');
        }

        if ($transaction->isExecuted()) {
            throw new TransactionExecutionException('Transaction is already executed');
        }

        $transaction->setExecuted(true);

        $this->accountManager->increaseBalance(
            $transaction->getAccount(),
            $transaction->getAmount(),
        );
    }
}
