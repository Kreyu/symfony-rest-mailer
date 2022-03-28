<?php

declare(strict_types=1);

namespace App\Mailer\Messenger\SendMessage;

use App\Mailer\Entity\Message;
use App\Mailer\Exception\MessageNotFoundException;
use App\Mailer\Mime\Factory\EmailFactory;
use App\Project\Exception\ProjectMailerDsnNotFoundException;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class SendMessageHandler implements MessageHandlerInterface
{
    public function __construct(
        private EntityManagerInterface $entityManager,
        private EmailFactory $emailFactory,
    ) {
        // ...
    }

    /**
     * @throws TransportExceptionInterface
     */
    public function __invoke(SendMessage $command): ?SentMessage
    {
        $message = $this->entityManager
            ->getRepository(Message::class)
            ->findOneByUuid($command->getMessageUuid());

        if (null === $message) {
            throw MessageNotFoundException::createForUuid($command->getMessageUuid());
        }

        $project = $message->getProject();

        if (null === $project) {
            throw new \InvalidArgumentException('Message has no related project.');
        }

        $dsn = $project->getMailerConfiguration()->getDsn();

        if (null === $dsn) {
            throw ProjectMailerDsnNotFoundException::createForProject($project);
        }

        $email = $this->emailFactory->create($message);

        return Transport::fromDsn($dsn)->send($email);
    }
}
