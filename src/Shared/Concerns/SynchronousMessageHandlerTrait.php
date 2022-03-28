<?php

declare(strict_types=1);

namespace App\Shared\Concerns;

use Symfony\Component\Messenger\HandleTrait;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Contracts\Service\Attribute\Required;

/**
 * Use this trait whenever you need to synchronously dispatch any messenger message!
 * This way you can instantly retrieve data returned in message handler.
 */
trait SynchronousMessageHandlerTrait
{
    use HandleTrait;

    #[Required]
    public function setMessageBus(MessageBusInterface $messageBus): void
    {
        $this->messageBus = $messageBus;
    }
}
