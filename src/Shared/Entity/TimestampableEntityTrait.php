<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

trait TimestampableEntityTrait
{
    #[ORM\Column(type: "datetime_immutable")]
    private DateTimeInterface $createdAt;

    protected function initializeTimestamps(): void
    {
        $this->createdAt = new DateTimeImmutable();
    }

    public function getCreatedAt(): DateTimeInterface
    {
        return $this->createdAt;
    }
}