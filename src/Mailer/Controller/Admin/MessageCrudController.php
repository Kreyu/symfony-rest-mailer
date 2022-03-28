<?php

declare(strict_types=1);

namespace App\Mailer\Controller\Admin;

use App\Mailer\Entity\Message;
use App\Mailer\Util\MessageRenderer;
use App\Shared\Controller\Admin\AbstractCrudController;
use Symfony\Component\HttpFoundation\Response;

class MessageCrudController extends AbstractCrudController
{
    public function __construct(
        private MessageRenderer $messageRenderer,
    ) {
        // ...
    }

    public function previewAction(): Response
    {
        $message = $this->admin->getSubject();

        if (!$message instanceof Message) {
            throw $this->createNotFoundException();
        }

        return $this->render('admin/mailer/message/preview.html.twig', [
            'body' => $this->messageRenderer->render($message),
        ]);
    }
}
