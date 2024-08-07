<?php

namespace App\Service;

use App\Entity\Account;
use Doctrine\ORM\EntityManagerInterface;

class AccountManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
    }

    public function increaseBalance(Account $account, string $amount): string
    {
        $account->setBalance(bcadd($account->getBalance(), $amount));

        $this->entityManager->flush();

        return $account->getBalance();
    }

    public function decreaseBalance(Account $account, string $amount): string
    {
        $account->setBalance(bcsub($account->getBalance(), $amount));

        $this->entityManager->flush();

        return $account->getBalance();
    }

    public function hasEnoughMoneyForPayout(Account $account, string $amount): bool
    {
        return bccomp(bcsub($account->getBalance(), $amount), '0') >= 0;
    }
}
