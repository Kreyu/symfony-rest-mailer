<?php

declare(strict_types=1);

namespace App\Mailer\Admin\Filter;

use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\Uid\Uuid;

class MessageAutocompleteTypeCallback
{
    /**
     * This method should be used as a "callback" field option of the {@see ModelAutocompleteFilter} filter type.
     */
    public static function filter(AdminInterface $admin, array $properties, $value): void
    {
        $value = trim($value);

        $datagrid = $admin->getDatagrid();
        $query = $datagrid->getQuery();

        $rootAlias = $query->getRootAliases()[0];

        $clauses = $query->expr()->orX();

        if (in_array('id', $properties, true)) {
            $clauses->add(sprintf('%s.id LIKE :id', $rootAlias));

            $query->setParameter('id', '%' . $value . '%');
        }

        if (in_array('subject', $properties, true)) {
            $clauses->add(sprintf('%s.subject LIKE :subject', $rootAlias));

            $query->setParameter('subject', '%' . $value . '%');
        }

        if (in_array('uuid', $properties, true) && Uuid::isValid($value)) {
            $clauses->add(sprintf('%s.uuid = :uuid', $rootAlias));

            $query->setParameter('uuid', Uuid::fromString($value)->toBinary());
        }

        if ($clauses->count() > 0) {
            $query->andWhere($clauses);
        }
    }
}
