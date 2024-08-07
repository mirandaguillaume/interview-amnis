<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\AlreadyExecutedTransaction;
use App\Exceptions\NotEnoughBalance;
use App\Exceptions\DateIncorrect;
use App\Exceptions\WrongTransactionType;
use DateTime;

class PayoutManager
{
    public function __construct(private readonly AccountManager $accountManager)
    {
    }

    public function execute(Transaction $transaction): void
    {
        if ($transaction->getType() !== TransactionTypeEnum::PAYOUT) {
            throw new WrongTransactionType(
                TransactionTypeEnum::PAYOUT,
                $transaction->getType(),
            );
        }

        if ($transaction->isExecuted()) {
            throw new AlreadyExecutedTransaction();
        }

        if ($transaction->getDate() > new \DateTimeImmutable()) {
            throw new DateIncorrect($transaction->getDate());
        }

        if (!$this->accountManager->hasEnoughMoneyForPayout(
            $transaction->getAccount(),
            $transaction->getAmount(),
        )) {
            throw new NotEnoughBalance();
        }

        $transaction->setExecuted(true);

        $this->accountManager->decreaseBalance(
            $transaction->getAccount(),
            $transaction->getAmount()
        );
    }
}
