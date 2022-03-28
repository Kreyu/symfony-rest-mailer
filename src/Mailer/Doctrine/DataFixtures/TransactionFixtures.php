<?php

declare(strict_types=1);

namespace App\Mailer\Doctrine\DataFixtures;

use App\Mailer\Entity\Message;
use App\Mailer\Messenger\SendMessage\SendMessage;
use App\Shared\Doctrine\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\MessageBusInterface;

class TransactionFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(private MessageBusInterface $messageBus)
    {
        parent::__construct();
    }

    public function load(ObjectManager $manager): void
    {
        $messages = $manager->getRepository(Message::class)->findAll();

        foreach ($messages as $message) {
            $this->messageBus->dispatch(new SendMessage(
                messageUuid: $message->getUuid(),
            ));
        }
    }

    public function getDependencies(): array
    {
        return [
            MessageFixtures::class,
        ];
    }
}
