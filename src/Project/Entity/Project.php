<?php

declare(strict_types=1);

namespace App\Project\Entity;

use App\Project\Entity\ValueObject\MailerConfiguration;
use App\Project\Repository\ProjectRepository;
use App\Shared\Entity\EntityInterface;
use App\Shared\Entity\TimestampableEntityInterface;
use App\Shared\Entity\TimestampableEntityTrait;
use App\Shared\Entity\UuidEntityTrait;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: ProjectRepository::class)]
class Project implements EntityInterface, TimestampableEntityInterface
{
    use UuidEntityTrait;
    use TimestampableEntityTrait;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    protected ?int $id = null;

    #[ORM\Column(type: "string", length: 255)]
    private string $name;

    #[ORM\Embedded(class: MailerConfiguration::class)]
    private MailerConfiguration $mailerConfiguration;

    public function __construct()
    {
        $this->mailerConfiguration = new MailerConfiguration();

        $this->initializeUuid();
        $this->initializeTimestamps();
    }

    public function __toString(): string
    {
        return $this->name ?? '';
    }

    public function getId(): null|int|string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getMailerConfiguration(): MailerConfiguration
    {
        return $this->mailerConfiguration;
    }
}
