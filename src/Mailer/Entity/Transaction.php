<?php

declare(strict_types=1);

namespace App\Mailer\Entity;

use ApiPlatform\Core\Annotation as ApiPlatform;
use App\Mailer\Repository\TransactionRepository;
use App\Shared\Entity\EntityInterface;
use App\Shared\Entity\IdEntityTrait;
use App\Shared\Entity\TimestampableEntityTrait;
use App\Shared\Entity\UuidEntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: TransactionRepository::class)]
#[ORM\Table(name: "mailer_transaction")]
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
class Transaction implements EntityInterface
{
    use IdEntityTrait;
    use UuidEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: Message::class, inversedBy: "transactions")]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private Message $message;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $transportMessageId;

    #[ORM\Column(type: "text", nullable: true)]
    private ?string $transportDebug;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $exceptionClass;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $exceptionMessage;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $exceptionCode;

    public function __construct()
    {
        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public function __toString(): string
    {
        return '#' . $this->id;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

    public function setMessage(Message $message): void
    {
        $this->message = $message;
    }

    public function getTransportMessageId(): ?string
    {
        return $this->transportMessageId;
    }

    public function setTransportMessageId(?string $transportMessageId): void
    {
        $this->transportMessageId = $transportMessageId;
    }

    public function getTransportDebug(): ?string
    {
        return $this->transportDebug;
    }

    public function setTransportDebug(?string $transportDebug): void
    {
        $this->transportDebug = $transportDebug;
    }

    public function getExceptionClass(): ?string
    {
        return $this->exceptionClass;
    }

    public function setExceptionClass(?string $exceptionClass): void
    {
        $this->exceptionClass = $exceptionClass;
    }

    public function getExceptionMessage(): ?string
    {
        return $this->exceptionMessage;
    }

    public function setExceptionMessage(?string $exceptionMessage): void
    {
        $this->exceptionMessage = $exceptionMessage;
    }

    public function getExceptionCode(): ?string
    {
        return $this->exceptionCode;
    }

    public function setExceptionCode(null|int|string $exceptionCode): void
    {
        if (is_int($exceptionCode)) {
            $exceptionCode = (string) $exceptionCode;
        }

        $this->exceptionCode = $exceptionCode;
    }
}
