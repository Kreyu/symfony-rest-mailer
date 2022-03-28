<?php

declare(strict_types=1);

namespace App\Mailer\Messenger\SendMessage;

use Symfony\Component\Uid\Uuid;

class SendMessage
{
    public function __construct(
        private Uuid $messageUuid,
    ) {
        // ...
    }
    
    public function getMessageUuid(): Uuid
    {
        return $this->messageUuid;
    }
}
