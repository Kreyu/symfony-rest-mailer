<?php

declare(strict_types=1);

namespace App\OAuth2\Admin;

use App\OAuth2\Entity\Client;
use App\Project\Entity\Project;
use App\Shared\Admin\AbstractAdmin;
use Exception;
use League\Bundle\OAuth2ServerBundle\Event\PreSaveClientEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Sonata\AdminBundle\Datagrid\DatagridInterface;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Security\Acl\Permission\AdminPermissionMap;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class AccessTokenAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'oauth2-access-token';

    protected function getAccessMapping(): array
    {
        return [
            'revoke' => AdminPermissionMap::PERMISSION_VIEW,
        ];
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->clearExcept(['list', 'delete'])
            ->add('revoke', $this->getRouterIdParameter().'/revoke');
    }

    public function configureDefaultSortValues(array &$sortValues): void
    {
        $sortValues[DatagridInterface::SORT_BY] = 'expiry';
        $sortValues[DatagridInterface::SORT_ORDER] = 'DESC';
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('identifier')
            ->add('client')
            ->add('expiry')
            ->add('revoked')
            ->add('_actions', fieldDescriptionOptions: [
                'actions' => [
                    'delete' => [],
                    'revoke' => [
                        'template' => 'admin/oauth2/access_token/action/list__action_revoke.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('basic')
                ->add('name')
                ->add('projects', EntityType::class, [
                    'multiple' => true,
                    'class' => Project::class,
                ])
                ->add('active')
            ->end();
    }

    /**
     * @param  object<Client> $object
     */
    protected function prePersist(object $object): void
    {
        $this->getEventDispatcher()->dispatch(new PreSaveClientEvent($object), OAuth2Events::PRE_SAVE_CLIENT);
    }

    /**
     * @throws Exception due to 'random_bytes' function
     */
    protected function createNewInstance(): object
    {
        return new Client(
            name: '',
            identifier: hash('md5', random_bytes(16)),
            secret: hash('sha512', random_bytes(32)),
        );
    }
}
