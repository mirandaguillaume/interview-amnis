<?php

namespace App\StateProcessor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Exchange;
use App\Entity\Transaction;
use App\Enums\TransactionTypeEnum;
use App\Exceptions\AlreadyExecutedTransaction;
use App\Exceptions\NotEnoughBalance;
use App\Exceptions\DateIncorrect;
use App\Exceptions\SameCurrencyExchange;
use App\Gateway\ExchangeRateGateway;
use App\Service\AccountManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<Exchange, Exchange|void>
 */
class ExchangeExecutionProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly PersistProcessor $persistProcessor,
        private readonly AccountManager $accountManager,
    )
    {}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
        if ($data->isExecuted() === true) {
            throw new AlreadyExecutedTransaction();
        }

        if ($data->getDate() > new \DateTimeImmutable()) {
            throw new DateIncorrect($data->getDate());
        }

        if ($data->getFromCurrency() === $data->getToCurrency()) {
            throw new SameCurrencyExchange();
        }

        $fromAccount = $data
            ->getBusinessPartner()
            ->getAccountByCurrency($data->getFromCurrency())
        ;

        $toAccount = $data
            ->getBusinessPartner()
            ->getAccountByCurrency($data->getToCurrency());

        if (!$this->accountManager->hasEnoughMoneyForPayout($fromAccount, $data->getFromAmount())) {
            throw new NotEnoughBalance();
        }

        $this->accountManager->decreaseBalance($fromAccount, $data->getFromAmount());
        $this->accountManager->increaseBalance($toAccount, $data->getToAmount());

        $data->setExecuted(true);

        $exchangePayoutTransaction = new Transaction();
        $exchangePayoutTransaction->setDate($data->getDate());
        $exchangePayoutTransaction->setAccount($fromAccount);
        $exchangePayoutTransaction->setAmount($data->getFromAmount());
        $exchangePayoutTransaction->setType(TransactionTypeEnum::EXCHANGE_PAYOUT);
        $exchangePayoutTransaction->setCountry($data->getBusinessPartner()->getCountry());
        $exchangePayoutTransaction->setIban('internal');
        $exchangePayoutTransaction->setName("Exchange payout to {$data->getToCurrency()->value} account");
        $exchangePayoutTransaction->setExecuted(true);


        $exchangePayinTransaction = new Transaction();
        $exchangePayinTransaction->setDate($data->getDate());
        $exchangePayinTransaction->setAccount($fromAccount);
        $exchangePayinTransaction->setAmount($data->getToAmount());
        $exchangePayinTransaction->setType(TransactionTypeEnum::EXCHANGE_PAYIN);
        $exchangePayinTransaction->setCountry($data->getBusinessPartner()->getCountry());
        $exchangePayinTransaction->setIban('internal');
        $exchangePayinTransaction->setName("Exchange payin from {$data->getFromCurrency()->value} account");
        $exchangePayinTransaction->setExecuted(true);

        $data->setPayoutTransaction($exchangePayoutTransaction);
        $data->setPayinTransaction($exchangePayinTransaction);

        $this->persistProcessor->process($data, $operation, $context);

        return $data;
    }
}
