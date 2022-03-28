<?php

declare(strict_types=1);

namespace App\Mailer\Repository;

use App\Mailer\Entity\Attachment;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @template-extends ServiceEntityRepository<Attachment>
 */
class AttachmentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Attachment::class);
    }

    public function findOneByUuid(Uuid $uuid): ?Attachment
    {
        return $this->findOneBy(['uuid' => $uuid->toBinary()]);
    }
}
