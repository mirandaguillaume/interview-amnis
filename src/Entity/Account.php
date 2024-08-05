<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use App\Enums\Currency;
use App\Repository\AccountRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Schema\UniqueConstraint;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: AccountRepository::class)]
#[ORM\Table(
    name: 'accounts',
    uniqueConstraints: [new UniqueConstraint(
        'business_currencies',
        ['business_partner_id', 'currency']
    )],
)]
#[ApiResource(
    operations: [
        new Get(),
        new Post(
            denormalizationContext: ['groups' => ['AccountCreate']]
        ),
        new GetCollection(),
    ],
    normalizationContext: ['groups' => ['AccountView']]
)]
#[UniqueEntity(
    fields: ['businessPartner', 'currency'],
    errorPath: 'currency',
)]
class Account
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: Types::INTEGER)]
    #[Groups(['AccountView'])]
    private int $id;

    #[ORM\Column(type: Types::STRING, enumType: Currency::class)]
    #[Groups(['AccountView', 'AccountCreate'])]
    private Currency $currency;

    #[ORM\Column(type: Types::DECIMAL, precision: 10, scale: 2)]
    #[Assert\NotBlank]
    #[Assert\GreaterThanOrEqual(0)]
    #[Groups(['AccountView'])]
    private string $balance = '0';

    #[ORM\ManyToOne(targetEntity: BusinessPartner::class, inversedBy: 'accounts')]
    #[ORM\JoinColumn(nullable: false)]
    #[Assert\NotBlank]
    #[Groups(['AccountView', 'AccountCreate'])]
    private BusinessPartner $businessPartner;

    #[ORM\OneToMany(targetEntity: Transaction::class, mappedBy: 'businessPartner')]
    #[Groups(['AccountView'])]
    private Collection $transactions;

    public function __construct()
    {
        $this->transactions = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    public function setCurrency(Currency $currency): void
    {
        $this->currency = $currency;
    }

    public function getBalance(): string
    {
        return $this->balance;
    }

    public function setBalance(string $balance): void
    {
        $this->balance = $balance;
    }

    public function getBusinessPartner(): BusinessPartner
    {
        return $this->businessPartner;
    }

    public function setBusinessPartner(BusinessPartner $businessPartner): void
    {
        $this->businessPartner = $businessPartner;
    }

    public function getTransactions(): Collection
    {
        return $this->transactions;
    }

    public function setTransactions(Collection $transactions): void
    {
        $this->transactions = $transactions;
    }

    public function addTransaction(Transaction $transaction): void
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
        }
    }

    public function removeTransaction(Transaction $transaction): void
    {
        $this->transactions->removeElement($transaction);
    }

    public function __toString(): string
    {
        return $this->businessPartner->getName().' - '.$this->currency->value;
    }
}
