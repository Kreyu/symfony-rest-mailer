<?php

declare(strict_types=1);

namespace App\Shared\Admin\Extension;

use App\Shared\Entity\EntityInterface;
use Sonata\AdminBundle\Admin\AbstractAdminExtension;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Route\RouteCollectionInterface;

class CommonAdminExtension extends AbstractAdminExtension
{
    public function configureDefaultSortValues(AdminInterface $admin, array &$sortValues): void
    {
        parent::configureDefaultSortValues($admin, $sortValues);

        if (is_a($admin->getClass(), EntityInterface::class, true)) {
            $sortValues[DatagridInterface::SORT_BY] = 'id';
            $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
        }
    }

    public function configureBatchActions(AdminInterface $admin, array $actions): array
    {
        return [];
    }
}
