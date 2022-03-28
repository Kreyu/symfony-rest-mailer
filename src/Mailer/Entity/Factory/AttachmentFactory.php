<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Factory;

use App\Mailer\Entity\Attachment;
use App\Mailer\Entity\Dto\AttachmentDto;

class AttachmentFactory
{
    public function createFromDto(AttachmentDto $dto): Attachment
    {
        $attachment = new Attachment();
        $attachment->setName($dto->name);
        $attachment->setContentType($dto->contentType);
        $attachment->setContentId($dto->contentId);

        return $attachment;
    }
}