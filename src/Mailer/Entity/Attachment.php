<?php

declare(strict_types=1);

namespace App\Mailer\Entity;

use ApiPlatform\Core\Annotation as ApiPlatform;
use App\Shared\Entity\EntityInterface;
use App\Shared\Entity\IdEntityTrait;
use App\Shared\Entity\TimestampableEntityTrait;
use App\Shared\Entity\UuidEntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "mailer_attachment")]
#[ApiPlatform\ApiResource(
    collectionOperations: [
        "get" => [
            "normalization_context" => [
                "groups" => [
                    "read.collection",
                ],
            ],
            "openapi_context" => [
                'parameters' => [
                    [
                        "name" => "Authorization",
                        "in" => "header",
                        "type" => "string",
                        "description" => "OAuth2 Bearer token"
                    ], [
                        "name" => "X-Project-UUID",
                        "in" => "header",
                        "type" => "string",
                        "description" => "UUID identifier of a project",
                    ],
                ],
            ],
        ],
    ],
    itemOperations: [
        "get" => [
            "normalization_context" => [
                "groups" => [
                    "read.item",
                ],
            ],
            "openapi_context" => [
                "parameters" => [
                    [
                        "name" => "Authorization",
                        "in" => "header",
                        "type" => "string",
                        "description" => "OAuth2 Bearer token"
                    ], [
                        "name" => "X-Project-UUID",
                        "in" => "header",
                        "type" => "string",
                        "description" => "UUID identifier of a project",
                    ],
                ],
            ],
        ],
    ],
    order: ["createdAt" => "DESC"],
    routePrefix: "/mailer",
)]
class Attachment implements EntityInterface
{
    use IdEntityTrait;
    use UuidEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: Message::class, cascade: ["persist"], inversedBy: "attachments")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Message $message = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $contentType = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $contentId = null;

    public function __construct()
    {
        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public function __toString(): string
    {
        return (string) $this->uuid;
    }

    public function getMessage(): ?Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function getContentType(): ?string
    {
        return $this->contentType;
    }

    public function setContentType(?string $contentType): void
    {
        $this->contentType = $contentType;
    }

    public function getContentId(): ?string
    {
        return $this->contentId;
    }

    public function setContentId(?string $contentId): void
    {
        $this->contentId = $contentId;
    }
}
