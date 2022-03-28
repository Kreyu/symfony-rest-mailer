<?php

declare(strict_types=1);

namespace App\Shared\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin as BaseAdmin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

abstract class AbstractAdmin extends BaseAdmin
{
    private ?EventDispatcherInterface $eventDispatcher = null;

    public function setEventDispatcher(EventDispatcherInterface $eventDispatcher): void
    {
        $this->eventDispatcher = $eventDispatcher;
    }

    protected function getEventDispatcher(): EventDispatcherInterface
    {
        if (null === $this->eventDispatcher) {
            throw new \LogicException(sprintf('Admin "%s" has no event dispatcher.', static::class));
        }

        return $this->eventDispatcher;
    }

    public function toString(object $object): string
    {
        $translator = $this->getTranslator();

        $label = $translator->trans(
            id: $this->getClassnameLabel(),
            domain: $this->getTranslationDomain(),
        );

        if ($this->isCurrentRoute('create')) {
            return $translator->trans('new', [], 'SonataAdminBundle') . ' ' . strtolower($label);
        }

        return $label . ' ' . parent::toString($object);
    }
}
