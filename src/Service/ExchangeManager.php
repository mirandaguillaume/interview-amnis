<?php

namespace App\Service;

use App\Entity\Exchange;
use Doctrine\ORM\EntityManagerInterface;

class ExchangeManager
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {}
}
