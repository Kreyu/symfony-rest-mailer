<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Dto;

use Symfony\Component\Validator\Constraints;

class MessageDto
{
    public ?string $subject = null;

    /**
     * @var array<AddressDto>
     */
    #[Constraints\Valid]
    #[Constraints\Count(min: 1)]
    #[Constraints\All(constraints: [
        new Constraints\Type(AddressDto::class),
    ])]
    public array $from = [];

    /**
     * @var array<AddressDto>
     */
    #[Constraints\Valid]
    #[Constraints\Count(min: 1)]
    #[Constraints\All(constraints: [
        new Constraints\Type(AddressDto::class),
    ])]
    public array $to = [];

    /**
     * @var array<AddressDto>
     */
    #[Constraints\Valid]
    #[Constraints\All(constraints: [
        new Constraints\Type(AddressDto::class),
    ])]
    public array $cc = [];

    /**
     * @var array<AddressDto>
     */
    #[Constraints\Valid]
    #[Constraints\All(constraints: [
        new Constraints\Type(AddressDto::class),
    ])]
    public array $bcc = [];

    /**
     * @var array<AddressDto>
     */
    #[Constraints\Valid]
    #[Constraints\All(constraints: [
        new Constraints\Type(AddressDto::class),
    ])]
    public array $replyTo = [];

    #[Constraints\Valid]
    #[Constraints\Type(type: AddressDto::class)]
    public ?AddressDto $sender = null;

    #[Constraints\Valid]
    #[Constraints\Type(type: AddressDto::class)]
    public ?AddressDto $returnPath = null;

    #[Constraints\NotBlank]
    public string $body;

    public ?string $charset = null;
    public ?\DateTimeInterface $date = null;
    public ?int $priority = null;

    /**
     * @var array<AttachmentDto>
     */
    #[Constraints\Valid]
    #[Constraints\All(constraints: [
        new Constraints\Type(AttachmentDto::class),
    ])]
    public array $attachments = [];
}
