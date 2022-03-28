<?php

declare(strict_types=1);

namespace App\Project\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Embeddable]
class MailerConfiguration
{
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $dsn;

    public function __construct(?string $dsn = null)
    {
        $this->dsn = $dsn;
    }

    public function getDsn(): ?string
    {
        return $this->dsn;
    }

    public function setDsn(?string $dsn): void
    {
        $this->dsn = $dsn;
    }
}
