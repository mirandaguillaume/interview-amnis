<?php

namespace App\Repository;

use App\Entity\BusinessPartner;
use App\Entity\Transaction;
use App\Enums\Currency;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findByBusinessPartnerAndCurrency(
        BusinessPartner $businessPartner,
        ?Currency       $currency = null,
    ): array
    {
        $queryBuilder = $this->createQueryBuilder('t');

        $queryBuilder
            ->leftJoin('t.account', 'a')
            ->andWhere('a.businessPartner = :businessPartner')
            ->setParameter('businessPartner', $businessPartner)
        ;

        if ($currency !== null) {
            $queryBuilder->andWhere('a.currency = :currency')
                ->setParameter('currency', $currency);
        }

        return $queryBuilder->getQuery()->getResult();
    }

}
