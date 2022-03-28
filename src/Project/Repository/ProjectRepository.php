<?php

declare(strict_types=1);

namespace App\Project\Repository;

use App\Project\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\Uid\Uuid;

/**
 * @template-extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function findOneByUuid(Uuid $uuid): ?Project
    {
        return $this->findOneBy(['uuid' => $uuid->toBinary()]);
    }
}
