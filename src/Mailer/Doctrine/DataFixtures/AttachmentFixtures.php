<?php

declare(strict_types=1);

namespace App\Mailer\Doctrine\DataFixtures;

use App\Mailer\Entity\Dto\AttachmentDto;
use App\Mailer\Entity\Factory\AttachmentFactory;
use App\Mailer\Entity\Message;
use App\Mailer\Exception\Base64DecodeException;
use App\Mailer\Util\AttachmentFileManager;
use App\Shared\Doctrine\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;

class AttachmentFixtures extends AbstractFixture implements DependentFixtureInterface
{
    public function __construct(
        private AttachmentFactory $attachmentFactory,
        private AttachmentFileManager $attachmentFileManager,
    ) {
        parent::__construct();
    }

    /**
     * @throws Base64DecodeException
     */
    public function load(ObjectManager $manager): void
    {
        $messages = $manager->getRepository(Message::class)->findAll();

        foreach ($messages as $message) {
            $attachmentDto = new AttachmentDto();
            $attachmentDto->base64 = base64_encode(file_get_contents(__DIR__ . '/image.jpg'));

            $attachment = $this->attachmentFactory->createFromDto($attachmentDto);
            $attachment->setMessage($message);

            $this->attachmentFileManager->saveFile($attachment, $attachmentDto->base64);

            $manager->persist($attachment);
            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return [
            MessageFixtures::class,
        ];
    }
}
