<?php

declare(strict_types=1);

namespace App\OAuth2\Controller\Admin;

use App\Shared\Controller\Admin\AbstractCrudController;
use League\Bundle\OAuth2ServerBundle\Manager\AccessTokenManagerInterface;
use League\Bundle\OAuth2ServerBundle\Model\AccessToken;
use Symfony\Component\HttpFoundation\Response;

class AccessTokenCrudController extends AbstractCrudController
{
    public function __construct(
        private AccessTokenManagerInterface $accessTokenManager,
    ) {
        // ...
    }

    public function revokeAction(): Response
    {
        $accessToken = $this->admin->getSubject();

        if (!$accessToken instanceof AccessToken) {
            throw $this->createNotFoundException();
        }

        $accessToken->revoke();

        $this->accessTokenManager->save($accessToken);

        return $this->redirectBackOrList();
    }
}
