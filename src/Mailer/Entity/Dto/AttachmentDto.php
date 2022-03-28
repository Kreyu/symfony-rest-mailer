<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Dto;

use Symfony\Component\Validator\Constraints;

class AttachmentDto
{
    #[Constraints\NotBlank]
    public string $base64;
    public ?string $name = null;
    public ?string $contentType = null;
    public ?string $contentId = null;
}
