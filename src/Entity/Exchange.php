<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Post;
use App\Enums\Currency;
use App\Repository\ExchangeRepository;
use App\StateProcessor\ExchangeExecutionProcessor;
use App\StateProcessor\ExchangePostProcessor;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ExchangeRepository::class)]
#[ORM\Table(name: 'exchanges')]
#[ApiResource(
    operations: [
        new GetCollection(),
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['ExchangeCreate']],
            processor: ExchangePostProcessor::class,
        ),
        new Patch(
            uriTemplate: '/exchanges/{id}/execute',
            denormalizationContext: ['groups' => ['ExchangeExecute']],
            processor: ExchangeExecutionProcessor::class,
        ),
    ],
    normalizationContext: ['groups' => ['ExchangeView']]
)]
class Exchange
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['ExchangeView'])]
    private int $id;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['ExchangeView'])]
    private string $exchangeRate;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\GreaterThan(0)]
    #[Groups(['ExchangeView', 'ExchangeCreate'])]
    private string $fromAmount;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2, nullable: true)]
    #[Groups(['ExchangeView'])]
    private string $toAmount;

    #[ORM\Column(type: Types::STRING, enumType: Currency::class)]
    #[Groups(['ExchangeView', 'ExchangeCreate'])]
    private Currency $fromCurrency;

    #[ORM\Column(type: Types::STRING, enumType: Currency::class)]
    #[Groups(['ExchangeView', 'ExchangeCreate'])]
    private Currency $toCurrency;

    #[ORM\ManyToOne(targetEntity: BusinessPartner::class)]
    #[Groups(['ExchangeView', 'ExchangeCreate'])]
    private BusinessPartner $businessPartner;

    #[ORM\Column(type: Types::DATETIME_IMMUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['ExchangeView', 'ExchangeCreate'])]
    private \DateTimeImmutable $date;

    #[ORM\Column(type: Types::BOOLEAN, nullable: false)]
    #[Groups(['ExchangeView'])]
    private bool $executed = false;

    #[ORM\OneToOne(targetEntity: Transaction::class, cascade: ['persist'])]
    #[Groups(['ExchangeView'])]
    private ?Transaction $payoutTransaction = null;

    #[ORM\OneToOne(targetEntity: Transaction::class, cascade: ['persist'])]
    #[Groups(['ExchangeView'])]
    private ?Transaction $payinTransaction = null;

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getExchangeRate(): string
    {
        return $this->exchangeRate;
    }

    public function setExchangeRate(string $exchangeRate): void
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function getFromAmount(): string
    {
        return $this->fromAmount;
    }

    public function setFromAmount(string $fromAmount): void
    {
        $this->fromAmount = $fromAmount;
    }

    public function getToAmount(): string
    {
        return $this->toAmount;
    }

    public function setToAmount(string $toAmount): void
    {
        $this->toAmount = $toAmount;
    }

    public function getFromCurrency(): Currency
    {
        return $this->fromCurrency;
    }

    public function setFromCurrency(Currency $fromCurrency): void
    {
        $this->fromCurrency = $fromCurrency;
    }

    public function getToCurrency(): Currency
    {
        return $this->toCurrency;
    }

    public function setToCurrency(Currency $toCurrency): void
    {
        $this->toCurrency = $toCurrency;
    }

    public function getBusinessPartner(): BusinessPartner
    {
        return $this->businessPartner;
    }

    public function setBusinessPartner(BusinessPartner $businessPartner): void
    {
        $this->businessPartner = $businessPartner;
    }

    public function getDate(): \DateTimeImmutable
    {
        return $this->date;
    }

    public function setDate(\DateTimeImmutable $date): void
    {
        $this->date = $date;
    }

    public function isExecuted(): bool
    {
        return $this->executed;
    }

    public function setExecuted(bool $executed): void
    {
        $this->executed = $executed;
    }

    public function getPayoutTransaction(): ?Transaction
    {
        return $this->payoutTransaction;
    }

    public function setPayoutTransaction(?Transaction $payoutTransaction): void
    {
        $this->payoutTransaction = $payoutTransaction;
    }

    public function getPayinTransaction(): ?Transaction
    {
        return $this->payinTransaction;
    }

    public function setPayinTransaction(?Transaction $payinTransaction): void
    {
        $this->payinTransaction = $payinTransaction;
    }
}
