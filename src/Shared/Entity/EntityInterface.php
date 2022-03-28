<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use Stringable;
use Symfony\Component\Uid\Uuid;

interface EntityInterface extends Stringable
{
    public function getId(): null|int|string;

    public function getUuid(): Uuid;
}
