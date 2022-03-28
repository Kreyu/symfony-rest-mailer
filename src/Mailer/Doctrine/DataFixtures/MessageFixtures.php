<?php

declare(strict_types=1);

namespace App\Mailer\Doctrine\DataFixtures;

use App\Mailer\Entity\Dto\AddressDto;
use App\Mailer\Entity\Dto\MessageDto;
use App\Mailer\Entity\Factory\MessageFactory;
use App\Mailer\Messenger\SendMessage\SendMessage;
use App\Project\Doctrine\DataFixtures\ProjectFixtures;
use App\Project\Entity\Project;
use App\Shared\Doctrine\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Messenger\MessageBusInterface;

class MessageFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private MessageFactory $messageFactory,
        private MessageBusInterface $messageBus,
    ) {
        parent::__construct();
    }

    public function load(ObjectManager $manager): void
    {
        $projects = $manager->getRepository(Project::class)->findAll();

        foreach ($projects as $project) {
            $messageDto = $this->createMessageDto();

            $message = $this->messageFactory->createFromDto($messageDto);
            $message->setProject($project);

            $manager->persist($message);
            $manager->flush();

            $this->messageBus->dispatch(new SendMessage(
                messageUuid: $message->getUuid(),
            ));
        }
    }

    public function getDependencies(): array
    {
        return [
            ProjectFixtures::class,
        ];
    }

    private function createMessageDto(): MessageDto
    {
        $dto = new MessageDto();
        $dto->subject = $this->faker->sentence;
        $dto->from = $this->createMultipleAddressDto();
        $dto->to = $this->createMultipleAddressDto();
        $dto->cc = $this->createMultipleAddressDto();
        $dto->bcc = $this->createMultipleAddressDto();
        $dto->replyTo = $this->createMultipleAddressDto();
        $dto->sender = $this->createAddressDto();
        $dto->returnPath = $this->createAddressDto();
        $dto->body = file_get_contents(__DIR__ . '/newsletter.html');
        $dto->charset = 'UTF-8';
        $dto->date = new \DateTimeImmutable();
        $dto->priority = 3;

        return $dto;
    }

    private function createAddressDto(): AddressDto
    {
        $dto = new AddressDto();
        $dto->address = $this->faker->safeEmail;
        $dto->name = $this->faker->name;

        return $dto;
    }

    private function createMultipleAddressDto(): array
    {
        $dto = [];

        for ($i = 1; $i <= $this->faker->numberBetween(1, 3); $i++) {
            $dto[] = $this->createAddressDto();
        }

        return $dto;
    }
}
