<?php

declare(strict_types=1);

namespace App\Mailer\Admin;

use App\Mailer\Admin\Filter\MessageAutocompleteTypeCallback;
use App\Mailer\Admin\Filter\MessageAutocompleteTypeToStringCallback;
use App\Mailer\Entity\Attachment;
use App\Mailer\Form\Type\AttachmentFileType;
use App\Shared\Admin\AbstractAdmin;
use App\Shared\Admin\Filter\UuidFilter;
use Knp\Menu\ItemInterface;
use Sonata\AdminBundle\Admin\AdminInterface;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Route\RouteCollectionInterface;
use Sonata\AdminBundle\Security\Acl\Permission\AdminPermissionMap;
use Sonata\DoctrineORMAdminBundle\Filter\CallbackFilter;
use Sonata\DoctrineORMAdminBundle\Filter\DateRangeFilter;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Sonata\Form\Type\DateRangePickerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class AttachmentAdmin extends AbstractAdmin
{
    protected $baseRoutePattern = 'mailer-attachment';

    protected function getAccessMapping(): array
    {
        return [
            'download' => AdminPermissionMap::PERMISSION_VIEW,
        ];
    }

    protected function configureRoutes(RouteCollectionInterface $collection): void
    {
        $collection
            ->remove('create')
            ->remove('delete')
            ->remove('export')
            ->add('download', $this->getRouterIdParameter().'/download');
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
            ->add('message.project', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
            ])
            ->add('message', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
                'template' => 'admin/mailer/attachment/field/list_message.html.twig',
            ])
            ->add('name', fieldDescriptionOptions: [
                'header_style' => 'width: 20%',
            ])
            ->add('contentType', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
            ])
            ->add('contentId', fieldDescriptionOptions: [
                'header_style' => 'width: 10%',
            ])
            ->add('_actions', fieldDescriptionOptions: [
                'header_style' => 'width: 15%',
                'actions' => [
                    'edit' => [],
                    'download' => [
                        'template' => 'admin/mailer/attachment/action/list__action_download.html.twig',
                    ],
                    'show_message' => [
                        'template' => 'admin/mailer/attachment/action/list__action_show_message.html.twig',
                    ],
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
            ->add('name')
            ->add('contentType')
            ->add('contentId')
            ->add('createdAt', DateRangeFilter::class, [
                'field_type' => DateRangePickerType::class,
            ]);
    }

    protected function configureFormFields(FormMapper $form): void
    {
        $form
            ->with('basic')
                ->add('name', TextType::class, [
                    'required' => false,
                ])
                ->add('contentType', TextType::class, [
                    'required' => false,
                ])
                ->add('contentId', TextType::class, [
                    'required' => false,
                    'help' => 'form.help_content_id',
                ])
                ->add('file', AttachmentFileType::class, [
                    'required' => false,
                    'mapped' => false,
                    'help' => 'form.help_file',
                ])
            ->end();
    }

    protected function configureTabMenu(ItemInterface $menu, string $action, ?AdminInterface $childAdmin = null): void
    {
        if (!$this->hasSubject()) {
            return;
        }

        /** @var Attachment $attachment */
        $attachment = $this->getSubject();

        $messageAdmin = $this->getConfigurationPool()->getAdminByAdminCode('mailer.admin.message');

        $menu
            ->addChild('action.show_message', [
                'uri' => $messageAdmin->generateObjectUrl('show', $attachment->getMessage()),
            ])
            ->setAttribute('icon', 'fa fa-envelope-open-text');
    }
}
