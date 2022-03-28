<?php

declare(strict_types=1);

namespace App\Mailer\ApiPlatform\Extension;

use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use App\Mailer\Entity\Message;
use App\Project\Entity\Project;
use App\Project\Exception\ProjectNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Uid\Uuid;

class ProjectMessageExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private RequestStack $requestStack
    ) {
        // ...
    }

    public function applyToCollection(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, string $operationName = null): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    public function applyToItem(QueryBuilder $queryBuilder, QueryNameGeneratorInterface $queryNameGenerator, string $resourceClass, array $identifiers, string $operationName = null, array $context = []): void
    {
        $this->addWhere($queryBuilder, $resourceClass);
    }

    private function addWhere(QueryBuilder $queryBuilder, string $resourceClass): void
    {
        if (Message::class !== $resourceClass) {
            return;
        }

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            throw new \RuntimeException('Unable to retrieve current request from the stack!');
        }

        $projectUuid = $request->headers->get('X-Project-UUID');

        if (null === $projectUuid || !Uuid::isValid($projectUuid)) {
            throw new \RuntimeException('Invalid project UUID given in request!');
        }

        $projectUuid = Uuid::fromString($projectUuid);

        $project = $this->entityManager
            ->getRepository(Project::class)
            ->findOneByUuid($projectUuid);

        if (null === $project) {
            throw ProjectNotFoundException::createForUuid($projectUuid);
        }

        $rootAlias = $queryBuilder->getRootAliases()[0];

        $queryBuilder
            ->andWhere(sprintf('%s.project = :project', $rootAlias))
            ->setParameter('project', $project);
    }
}
