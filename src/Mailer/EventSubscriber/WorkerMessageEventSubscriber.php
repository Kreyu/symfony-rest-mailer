<?php

declare(strict_types=1);

namespace App\Mailer\EventSubscriber;

use App\Mailer\Entity\Factory\TransactionFactory;
use App\Mailer\Entity\Message;
use App\Mailer\Messenger\SendMessage\SendMessage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\Event\WorkerMessageFailedEvent;
use Symfony\Component\Messenger\Event\WorkerMessageHandledEvent;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class WorkerMessageEventSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private TransactionFactory $transactionFactory,
    ) {
        // ...
    }

    public static function getSubscribedEvents(): array
    {
        return [
            WorkerMessageHandledEvent::class => ['onWorkerMessageHandled'],
            WorkerMessageFailedEvent::class => ['onWorkerMessageFailed'],
        ];
    }

    public function onWorkerMessageHandled(WorkerMessageHandledEvent $event): void
    {
        $envelope = $event->getEnvelope();

        $message = $this->getMessageFromEnvelope($envelope);

        if (null === $message) {
            return;
        }

        /** @var null|HandledStamp $stamp */
        $stamp = $envelope->last(HandledStamp::class);

        if (null === $stamp) {
            return;
        }

        $result = $stamp->getResult();

        if (!$result instanceof SentMessage) {
            return;
        }

        $transaction = $this->transactionFactory->createFromSentMessage($result);
        $transaction->setMessage($message);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    public function onWorkerMessageFailed(WorkerMessageFailedEvent $event): void
    {
        $envelope = $event->getEnvelope();

        $message = $this->getMessageFromEnvelope($envelope);

        if (null === $message) {
            return;
        }

        /** @var null|ErrorDetailsStamp $stamp */
        $stamp = $envelope->last(ErrorDetailsStamp::class);

        if (null === $stamp) {
            return;
        }

        $transaction = $this->transactionFactory->createFromErrorDetailsStamp($stamp);
        $transaction->setMessage($message);

        $this->entityManager->persist($transaction);
        $this->entityManager->flush();
    }

    private function getMessageFromEnvelope(Envelope $envelope): ?Message
    {
        $message = $envelope->getMessage();

        if (!$message instanceof SendMessage) {
            return null;
        }

        return $this->entityManager
            ->getRepository(Message::class)
            ->findOneByUuid($message->getMessageUuid());
    }
}
