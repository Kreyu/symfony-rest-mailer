<?php

declare(strict_types=1);

namespace App\Mailer\Admin;

use App\Shared\Admin\AbstractAdmin;
use App\Shared\Admin\Filter\UuidFilter;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Security\Acl\Permission\AdminPermissionMap;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\Form\Type\DateRangePickerType;

class MessageAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'mailer-message';

    protected function getAccessMapping(): array
    {
        return [
            'preview' => AdminPermissionMap::PERMISSION_VIEW,
        ];
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('edit')
            ->remove('delete')
            ->remove('export')
            ->add('preview', $this->getRouterIdParameter().'/preview');
    }

    protected function configureListFields(ListMapper $list): void
    {
        $list
            ->add('id', fieldDescriptionOptions: [
                'header_style' => 'width: 5%',
            ])
            ->add('uuid', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
            ])
            ->add('project', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
                'collapse' => true,
            ])
            ->add('subject', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'collapse' => true,
            ])
            ->add('from', fieldDescriptionOptions: [
                'template' => 'admin/mailer/message/field/list_address_collection.html.twig',
                'header_style' => 'width: 15%',
                'collapse' => true,
            ])
            ->add('to', fieldDescriptionOptions: [
                'template' => 'admin/mailer/message/field/list_address_collection.html.twig',
                'header_style' => 'width: 15%',
                'collapse' => true,
            ])
            ->add('createdAt', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
                'format' => 'd/m/Y H:i:s'
            ])
            ->add('_actions', fieldDescriptionOptions: [
                'header_style' => 'width: 15%',
                'actions' => [
                    'show' => [
                        'template' => 'admin/shared/field/list__action_show.html.twig',
                    ],
                    'edit' => [],
                    'delete' => [],
                    'list_transactions' => [
                        'template' => 'admin/mailer/message/action/list__action_list_transactions.html.twig',
                    ],
                    'list_attachments' => [
                        'template' => 'admin/mailer/message/action/list__action_list_attachments.html.twig',
                    ],
                    'preview' => [
                        'template' => 'admin/mailer/message/action/list__action_preview.html.twig',
                    ],
                ],
            ]);
    }

    protected function configureDatagridFilters(DatagridMapper $filter): void
    {
        $filter
            ->add('id')
            ->add('uuid', CallbackFilter::class, [
                'callback' => [UuidFilter::class, 'filter'],
            ])
            ->add('project')
            ->add('subject')
            ->add('from')
            ->add('to')
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
                ->add('project')
                ->add('createdAt')
            ->end()
            ->with('metadata')
                ->add('subject')
                ->add('from', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address_collection.html.twig',
                ])
                ->add('to', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address_collection.html.twig',
                ])
                ->add('replyTo', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address_collection.html.twig',
                ])
                ->add('cc', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address_collection.html.twig',
                ])
                ->add('bcc', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address_collection.html.twig',
                ])
                ->add('sender', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address.html.twig',
                ])
                ->add('returnPath', fieldDescriptionOptions: [
                    'template' => 'admin/mailer/message/field/show_address.html.twig',
                ])
                ->add('priority')
                ->add('date')
                ->add('charset')
            ->end();
    }

    protected function configureTabMenu(ItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        $transactionAdmin = $this->getChild('mailer.admin.transaction');
        $attachmentAdmin = $this->getChild('mailer.admin.attachment');

        if ($this->hasSubject()) {
            if (null !== $childAdmin) {
                $menu
                    ->addChild('action.back_to_show', [
                        'uri' => $this->generateObjectUrl('show', $this->getSubject()),
                    ])
                    ->setAttribute('icon', 'fa fa-arrow-left');
            }

            if (null === $childAdmin || !is_a($childAdmin, get_class($transactionAdmin))) {
                $menu
                    ->addChild('action.list_transactions', [
                        'uri' => $transactionAdmin->generateUrl('list'),
                    ])
                    ->setAttribute('icon', 'fa fa-history');
            }

            if (null === $childAdmin || !is_a($childAdmin, get_class($attachmentAdmin))) {
                $menu
                    ->addChild('action.list_attachments', [
                        'uri' => $attachmentAdmin->generateUrl('list'),
                    ])
                    ->setAttribute('icon', 'fa fa-file-alt');
            }

            $menu
                ->addChild('action.preview', [
                    'uri' => $this->generateObjectUrl('preview', $this->getSubject()),
                ])
                ->setAttribute('icon', 'fa fa-eye')
                ->setLinkAttribute('target', '_blank');
        }
    }
}
