<?php

declare(strict_types=1);

namespace App\Mailer\Repository;

use App\Mailer\Entity\Message;
use App\Mailer\Entity\Transaction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @template-extends ServiceEntityRepository<Transaction>
 */
class TransactionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Transaction::class);
    }

    public function findOneByUuid(Uuid $uuid): ?Message
    {
        return $this->findOneBy(['uuid' => $uuid->toBinary()]);
    }
}
