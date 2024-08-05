<?php

declare(strict_types=1);

namespace App\Tests\Services;

use App\Entity\Account;
use App\Entity\BusinessPartner;
use App\Enums\Currency;
use App\Service\AccountManager;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class AccountManagerTest extends WebTestCase
{
    private AccountManager $accountManager;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();
        $container = static::getContainer();

        $this->accountManager = $container->get(AccountManager::class);
    }

    public function testPayinBalanceChange(): void
    {
        $account = $this->createAccount();

        $this->accountManager->increaseBalance($account, '1000');

        $this->assertEquals('11000', $account->getBalance());
    }

    public function testPayoutBalanceChange(): void
    {
        $account = $this->createAccount();

        $this->accountManager->decreaseBalance($account, '1000');

        $this->assertEquals('9000', $account->getBalance());
    }

    /**
     * @dataProvider providePayoutBalanceResult
     */
    public function testHasEnoughMoneyForPayout(string $amount, bool $result): void
    {
        $this->assertEquals(
            $result,
            $this->accountManager->hasEnoughMoneyForPayout($this->createAccount(), $amount),
        );
    }

    public function providePayoutBalanceResult(): \Generator
    {
        yield 'enough money' => [
            'amount' => '1000',
            'result' => true,
        ];
        yield 'not enough money' => [
            'amount' => '11000',
            'result' => false,
        ];
    }

    private function createAccount(): Account
    {
        $account = new Account();

        $account->setBalance('10000');
        $account->setCurrency(Currency::EUR);
        $account->setBusinessPartner(new BusinessPartner());

        return $account;
    }
}
