<?php

declare(strict_types=1);

namespace App\Shared\Admin\EventListener;

use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Event\ConfigureMenuEvent;
use Symfony\Component\HttpFoundation\RequestStack;

class ActiveItemMenuListener
{
    private RequestStack $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function configure(ConfigureMenuEvent $event): void
    {
        $menu = $event->getMenu();

        $request = $this->requestStack->getCurrentRequest();

        if (null === $request) {
            return;
        }

        $adminCode = $request->attributes->get('_sonata_admin');

        if (null === $adminCode) {
            return;
        }

        // If current admin code is separated by "|", use the last part.
        $adminCodeParts = explode('|', $adminCode);

        $this->configureItem($menu, end($adminCodeParts));
    }

    private function configureItem(ItemInterface $item, string $adminCode): void
    {
        foreach ($item->getChildren() as $child) {
            if ($child->hasChildren()) {
                $this->configureItem($child, $adminCode);
            }

            /** @var null|AdminInterface $admin */
            $admin = $child->getExtra('admin');

            $child->setCurrent(null !== $admin && $admin->getCode() === $adminCode);
        }
    }
}