<?php

declare(strict_types=1);

namespace App\Mailer\Repository;

use App\Mailer\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @template-extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findOneByUuid(Uuid $uuid): ?Message
    {
        return $this->findOneBy(['uuid' => $uuid->toBinary()]);
    }
}
