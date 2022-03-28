<?php

declare(strict_types=1);

namespace App\Project\Exception;

use RuntimeException;
use Symfony\Component\Uid\Uuid;

class ProjectNotFoundException extends RuntimeException
{
    public static function createForUuid(Uuid $uuid): static
    {
        return new static(
            message: "Project with given UUID \"{$uuid}\" does not exist."
        );
    }
}
