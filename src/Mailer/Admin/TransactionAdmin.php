<?php

declare(strict_types=1);

namespace App\Mailer\Admin;

use App\Mailer\Admin\Filter\MessageAutocompleteTypeCallback;
use App\Mailer\Admin\Filter\MessageAutocompleteTypeToStringCallback;
use App\Shared\Admin\AbstractAdmin;
use App\Shared\Admin\Filter\UuidFilter;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DateRangePickerType;

class TransactionAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'mailer-transaction';

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection->clearExcept(['list', 'show']);
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id')
            ->add('uuid')
            ->add('message.project')
            ->add('message', fieldDescriptionOptions: [
                'template' => 'admin/mailer/transaction/field/list_message.html.twig',
                'header_style' => 'width: 15%',
                'collapse' => true,
            ])
            ->add('transportMessageId', fieldDescriptionOptions: [
                'template' => 'admin/mailer/transaction/field/list_transport_message_id.html.twig',
                'header_style' => 'width: 15%',
                'collapse' => true,
            ])
            ->add('exceptionMessage', fieldDescriptionOptions: [
                'template' => 'admin/mailer/transaction/field/list_exception_message.html.twig',
                'header_style' => 'width: 15%',
                'collapse' => true,
            ])
            ->add('createdAt')
            ->add('_actions', fieldDescriptionOptions: [
                'actions' => [
                    'show' => [],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        if ($this->isChild()) {
            return;
        }

        $filter
            ->add('id')
            ->add('uuid', CallbackFilter::class, [
                'callback' => [UuidFilter::class, 'filter'],
            ])
            ->add('message.project')
            ->add('message', ModelAutocompleteFilter::class, [
                'field_options' => [
                    'minimum_input_length' => 1,
                    'property' => ['id', 'uuid', 'subject'],
                    'callback' => [MessageAutocompleteTypeCallback::class, 'filter'],
                    'to_string_callback' => [MessageAutocompleteTypeToStringCallback::class, 'toString'],
                ],
            ])
            ->add('transportMessageId')
            ->add('transportDebug')
            ->add('exceptionCode')
            ->add('exceptionClass')
            ->add('exceptionMessage')
            ->add('createdAt', DateRangeFilter::class, [
                'field_type' => DateRangePickerType::class,
            ]);
    }

    protected function configureShowFields(ShowMapper $show): void
    {
        $show
            ->with('basic')
                ->add('id')
                ->add('uuid')
                ->add('message.project')
                ->add('message')
                ->add('createdAt')
            ->end();

        $transaction = $this->getSubject();

        if (null !== $transaction->getTransportMessageId()) {
            $show
                ->with('details')
                    ->add('transportMessageId')
                    ->add('transportDebug', fieldDescriptionOptions: [
                        'template' => 'admin/mailer/transaction/field/show_debug.html.twig',
                    ])
                ->end();
        }

        if (null !== $transaction->getExceptionMessage()) {
            $show
                ->with('details')
                    ->add('exceptionCode')
                    ->add('exceptionClass')
                    ->add('exceptionMessage')
                ->end();
        }
    }
}
