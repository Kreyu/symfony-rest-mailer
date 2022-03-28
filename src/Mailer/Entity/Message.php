<?php

declare(strict_types=1);

namespace App\Mailer\Entity;

use ApiPlatform\Core\Annotation as ApiPlatform;
use App\Mailer\Controller\Api\CreateMessageController;
use App\Mailer\Entity\ValueObject\Address;
use App\Mailer\Repository\MessageRepository;
use App\Project\Entity\Project;
use App\Shared\Entity\EntityInterface;
use App\Shared\Entity\IdEntityTrait;
use App\Shared\Entity\TimestampableEntityTrait;
use App\Shared\Entity\UuidEntityTrait;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: MessageRepository::class)]
#[ORM\Table(name: "mailer_message")]
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
        "post" => [
            "controller" => CreateMessageController::class,
            "normalization_context" => [
                "groups" => [
                    "write",
                ],
            ],
            "openapi_context" => [
                "summary" => "Creates a Message resource and queues it for sending.",
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
class Message implements EntityInterface
{
    use IdEntityTrait,
        UuidEntityTrait,
        TimestampableEntityTrait;

    #[ORM\ManyToOne(targetEntity: Project::class)]
    #[ORM\JoinColumn(nullable: false, onDelete: "CASCADE")]
    private ?Project $project = null;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $subject = null;

    #[ORM\Column(name: "`from`", type: "address_json")]
    private array $from = [];

    #[ORM\Column(name: "`to`", type: "address_json")]
    private array $to = [];

    #[ORM\Column(type: "address_json")]
    private array $cc = [];

    #[ORM\Column(type: "address_json")]
    private array $bcc = [];

    #[ORM\Column(type: "address_json")]
    private array $replyTo = [];

    #[ORM\Embedded(class: Address::class)]
    private Address $sender;

    #[ORM\Embedded(class: Address::class)]
    private Address $returnPath;

    #[ORM\Column(type: "text")]
    private string $body;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $charset = null;

    #[ORM\Column(type: "datetime", nullable: true)]
    private ?DateTimeInterface $date = null;

    #[ORM\Column(type: "integer", nullable: true)]
    private ?int $priority = null;

    #[ORM\OneToMany(mappedBy: "message", targetEntity: Transaction::class)]
    #[ApiPlatform\ApiSubresource]
    private Collection $transactions;

    #[ORM\OneToMany(mappedBy: "message", targetEntity: Attachment::class, cascade: ["persist"])]
    #[ApiPlatform\ApiSubresource]
    private Collection $attachments;

    public function __construct()
    {
        $this->sender = new Address();
        $this->returnPath = new Address();

        $this->transactions = new ArrayCollection();
        $this->attachments = new ArrayCollection();

        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public function __toString(): string
    {
        if ($this->getSubject()) {
            return '"' . $this->subject . '"';
        }

        return (string) $this->uuid;
    }

    public function getProject(): ?Project
    {
        return $this->project;
    }

    public function setProject(?Project $project): void
    {
        $this->project = $project;
    }

    public function getSubject(): ?string
    {
        return $this->subject;
    }

    public function setSubject(?string $subject): void
    {
        $this->subject = $subject;
    }

    /**
     * @return array<Address>
     */
    public function getFrom(): array
    {
        return $this->from;
    }

    public function setFrom(array $from): void
    {
        $this->from = $from;
    }

    public function addFrom(Address $address): void
    {
        $this->from[] = $address;
    }

    /**
     * @return array<Address>
     */
    public function getTo(): array
    {
        return $this->to;
    }

    public function setTo(array $to): void
    {
        $this->to = $to;
    }

    public function addTo(Address $address): void
    {
        $this->to[] = $address;
    }

    /**
     * @return array<Address>
     */
    public function getCc(): array
    {
        return $this->cc;
    }

    public function setCc(array $cc): void
    {
        $this->cc = $cc;
    }

    public function addCc(Address $address): void
    {
        $this->cc[] = $address;
    }

    /**
     * @return array<Address>
     */
    public function getBcc(): array
    {
        return $this->bcc;
    }

    public function setBcc(array $bcc): void
    {
        $this->bcc = $bcc;
    }

    public function addBcc(Address $address): void
    {
        $this->bcc[] = $address;
    }

    /**
     * @return array<Address>
     */
    public function getReplyTo(): array
    {
        return $this->replyTo;
    }

    public function addReplyTo(Address $address): void
    {
        $this->replyTo[] = $address;
    }

    public function setReplyTo(array $replyTo): void
    {
        $this->replyTo = $replyTo;
    }

    public function getDate(): ?DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(?DateTimeInterface $date): void
    {
        $this->date = $date;
    }

    public function getSender(): ?Address
    {
        // Doctrine embedded objects cannot be null.

        if (empty($this->sender->getAddress())) {
            return null;
        }

        return $this->sender;
    }

    public function setSender(?Address $sender): void
    {
        if (null === $sender) {
            $this->sender->setName(null);
            $this->sender->setAddress(null);
        } else {
            $this->sender->setName($sender->getName());
            $this->sender->setAddress($sender->getAddress());
        }
    }

    public function getReturnPath(): ?Address
    {
        // Doctrine embedded objects cannot be null.

        if (empty($this->returnPath->getAddress())) {
            return null;
        }

        return $this->returnPath;
    }

    public function setReturnPath(?Address $returnPath): void
    {
        if (null === $returnPath) {
            $this->returnPath->setName(null);
            $this->returnPath->setAddress(null);
        } else {
            $this->returnPath->setName($returnPath->getName());
            $this->returnPath->setAddress($returnPath->getAddress());
        }
    }

    public function getBody(): string
    {
        return $this->body;
    }

    public function setBody(string $body): void
    {
        $this->body = $body;
    }

    public function getCharset(): ?string
    {
        return $this->charset;
    }

    public function setCharset(?string $charset): void
    {
        $this->charset = $charset;
    }

    public function getPriority(): ?int
    {
        return $this->priority;
    }

    public function setPriority(?int $priority): void
    {
        $this->priority = $priority;
    }

    public function getTransactions(): ArrayCollection|Collection
    {
        return $this->transactions;
    }

    public function addTransaction(Transaction $transaction): void
    {
        if (!$this->transactions->contains($transaction)) {
            $this->transactions->add($transaction);
        }
    }

    public function removeTransaction(Transaction $transaction): void
    {
        if ($this->transactions->contains($transaction)) {
            $this->transactions->removeElement($transaction);
        }
    }

    public function getAttachments(): Collection
    {
        return $this->attachments;
    }

    public function setAttachments(Collection $attachments): void
    {
        $this->attachments = $attachments;
    }

    public function addAttachment(Attachment $attachment): void
    {
        if (!$this->attachments->contains($attachment)) {
            $this->attachments->add($attachment);
        }
    }

    public function removeAttachment(Attachment $attachment): void
    {
        if ($this->attachments->contains($attachment)) {
            $this->attachments->removeElement($attachment);
        }
    }
}
