<?php

declare(strict_types=1);

namespace App\Mailer\Exception;

use RuntimeException;
use Symfony\Component\Uid\Uuid;

class MessageNotFoundException extends RuntimeException
{
    public static function createForUuid(Uuid $uuid): static
    {
        return new static(
            message: "Message with given UUID \"{$uuid}\" does not exist."
        );
    }
}
