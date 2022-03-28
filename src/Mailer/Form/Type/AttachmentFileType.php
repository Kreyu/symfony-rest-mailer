<?php

declare(strict_types=1);

namespace App\Mailer\Form\Type;

use App\Mailer\Entity\Attachment;
use App\Mailer\Util\AttachmentFileManager;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Contracts\Translation\TranslatorInterface;

class AttachmentFileType extends FileType
{
    private AttachmentFileManager $attachmentProvider;

    public function __construct(AttachmentFileManager $attachmentProvider, TranslatorInterface $translator = null)
    {
        parent::__construct($translator);

        $this->attachmentProvider = $attachmentProvider;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        parent::buildForm($builder, $options);

        $builder->addEventListener(FormEvents::POST_SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $data = $event->getData();

            $attachment = $form->getParent()?->getData();

            if ($attachment instanceof Attachment && $data instanceof File) {
                $this->attachmentProvider->saveFile($attachment, $data->getContent());
            }
        });
    }
}
