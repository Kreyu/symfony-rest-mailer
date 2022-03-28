<?php

declare(strict_types=1);

namespace App\Mailer\Controller\Admin;

use App\Mailer\Entity\Attachment;
use App\Mailer\Util\AttachmentFileManager;
use App\Shared\Controller\Admin\AbstractCrudController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;

class AttachmentCrudController extends AbstractCrudController
{
    public function __construct(private AttachmentFileManager $attachmentProvider)
    {
        // ...
    }

    public function downloadAction(): Response
    {
        $attachment = $this->admin->getSubject();

        if (!$attachment instanceof Attachment) {
            throw $this->createNotFoundException();
        }

        try {
            $file = $this->attachmentProvider->getFile($attachment);
        } catch (FileNotFoundException) {
            $this->addFlash('error', $this->trans('flashes.file_not_found', [
                '%attachment%' => $attachment,
            ]));

            return $this->redirectBackOrList();
        }

        $filename = sprintf('mailer_attachment_%s', $attachment->getUuid());

        if ($extension = $file->guessExtension()) {
            $filename .= '.' . $extension;
        }

        $response = new BinaryFileResponse($file);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $filename);

        return $response;
    }
}
