<?php

namespace App\Controller\Api;

use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\TransactionExecutionException;
use App\Service\AccountManager;
use DateTime;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PayoutController extends AbstractController
{
    public function __construct(private readonly AccountManager $accountManager)
    {
    }

    public function __invoke(Transaction $transaction)
    {
        if ($transaction->getDate() > (new DateTime())) {
            throw new TransactionExecutionException('Payout transaction date can be only on the current date');
        }

        if (!$this->accountManager->hasEnoughMoneyForPayout(
            $transaction->getAccount(),
            $transaction->getAmount()
        )) {
            throw new TransactionExecutionException('You do not have enough money for a payout');
        }

        $transaction->setType(TransactionTypeEnum::PAYOUT);

        return $transaction;
    }
}
