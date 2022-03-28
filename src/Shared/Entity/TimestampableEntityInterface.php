<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use DateTimeInterface;

interface TimestampableEntityInterface
{
    public function getCreatedAt(): DateTimeInterface;
}