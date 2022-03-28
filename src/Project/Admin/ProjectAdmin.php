<?php

declare(strict_types=1);

namespace App\Project\Admin;

use Sonata\AdminBundle\Admin\AbstractAdmin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\FieldDescription\FieldDescriptionInterface;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;

class ProjectAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'project';

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', fieldDescriptionOptions: [
                'header_style' => 'width: 5%',
            ])
            ->add('uuid', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
            ])
            ->add('name', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
            ])
            ->add('mailerConfiguration.dsn', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'template' => 'admin/project/field/list_mailer_configuration_dsn.html.twig',
            ])
            ->add('_actions', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
                'actions' => [
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
            ->end()
            ->with('mailer')
                ->add('mailerConfiguration.dsn')
            ->end();
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('basic')
                ->add('id')
                ->add('uuid')
                ->add('name')
            ->end()
            ->with('mailer')
                ->add('mailerConfiguration.dsn')
            ->end();
    }
}
