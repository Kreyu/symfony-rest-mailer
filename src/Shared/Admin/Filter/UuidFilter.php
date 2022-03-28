<?php

declare(strict_types=1);

namespace App\Shared\Admin\Filter;

use Sonata\AdminBundle\Filter\Model\FilterData;
use Sonata\DoctrineORMAdminBundle\Datagrid\ProxyQueryInterface;
use Symfony\Component\Uid\Uuid;
use Throwable;

class UuidFilter
{
    public static function filter(ProxyQueryInterface $query, string $alias, string $field, FilterData $data): bool
    {
        if (!$data->hasValue()) {
            return false;
        }

        $value = trim($data->getValue());

        try {
            $value = Uuid::fromString($value);
        } catch (Throwable) {
            return false;
        }

        $parameter = sprintf('uuid_%d', $query->getUniqueParameterId());

        $query
            ->andWhere(sprintf('%s.%s = :%s', $alias, $field, $parameter))
            ->setParameter($parameter, $value->toBinary());

        return true;
    }
}
