<?php

namespace App\StateProcessor;

use ApiPlatform\Doctrine\Common\State\PersistProcessor;
use ApiPlatform\Metadata\Operation;
use ApiPlatform\State\ProcessorInterface;
use App\Entity\Exchange;
use App\Exceptions\NotEnoughBalance;
use App\Exceptions\DateIncorrect;
use App\Exceptions\SameCurrencyExchange;
use App\Gateway\ExchangeRateGateway;
use App\Service\AccountManager;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

/**
 * @implements ProcessorInterface<Exchange, Exchange|void>
 */
class ExchangePostProcessor implements ProcessorInterface
{
    public function __construct(
        #[Autowire(service: 'api_platform.doctrine.orm.state.persist_processor')]
        private readonly PersistProcessor $persistProcessor,
        private readonly ExchangeRateGateway $exchangeRateGateway,
        private readonly AccountManager $accountManager,
    )
    {}


    public function process(mixed $data, Operation $operation, array $uriVariables = [], array $context = []): mixed
    {
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

        // Check if the business partner has an account with the destination currency
        $toAccount = $data
            ->getBusinessPartner()
            ->getAccountByCurrency($data->getToCurrency());

        if (!$this->accountManager->hasEnoughMoneyForPayout($fromAccount, $data->getFromAmount())) {
            throw new NotEnoughBalance();
        }

        $data->setExchangeRate(
            $this->exchangeRateGateway->getExchangeRate(
                $data->getFromCurrency(),
                $data->getToCurrency(),
            ),
        );

        $data->setToAmount($data->getFromAmount() * $data->getExchangeRate());

        $this->persistProcessor->process($data, $operation, $context);

        return $data;
    }
}
