<?php

declare(strict_types=1);

namespace App\OAuth2\Admin;

use App\OAuth2\Entity\Client;
use App\Project\Entity\Project;
use App\Shared\Admin\AbstractAdmin;
use Exception;
use League\Bundle\OAuth2ServerBundle\Event\PreSaveClientEvent;
use League\Bundle\OAuth2ServerBundle\OAuth2Events;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class ClientAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'oauth2-client';

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('show')
            ->remove('export');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('identifier', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'collapse' => true,
            ])
            ->add('name', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'collapse' => true,
            ])
            ->add('projects', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'collapse' => true,
            ])
            ->add('active', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'collapse' => true,
            ])
            ->add('_actions', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'actions' => [
                    'copy_secret_token' => [
                        'template' => 'admin/oauth2/client/field/list__action_copy_secret_token.html.twig',
                    ],
                    'show' => [],
                    'edit' => [],
                    'delete' => [],
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
