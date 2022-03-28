<?php

declare(strict_types=1);

namespace App\Shared\Controller\Admin;

use Sonata\AdminBundle\Controller\CRUDController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\RequestStack;

abstract class AbstractCrudController extends CRUDController
{
    public function __construct(
        private RequestStack $requestStack,
    ) {
        // ...
    }

    protected function redirectBackOrList(): RedirectResponse
    {
        $request = $this->requestStack->getCurrentRequest();

        if (null !== $request && $referer = $request->attributes->get('referer')) {
            return $this->redirect($referer);
        }

        return $this->redirectToList();
    }
}
