<?php

declare(strict_types=1);

namespace App\Mailer\Mime\Factory;

use App\Mailer\Entity\Attachment;
use App\Mailer\Entity\Message;
use App\Mailer\Entity\ValueObject\Address;
use App\Mailer\Util\AttachmentFileManager;
use Symfony\Component\Mime\Address as MimeAddress;
use Symfony\Component\Mime\Email;

class EmailFactory
{
    public function __construct(
        private AddressFactory        $addressFactory,
        private AttachmentFileManager $attachmentProvider,
    ) {
        // ...
    }

    public function create(Message $message): Email
    {
        $email = new Email();

        if ($subject = $message->getSubject()) {
            $email->subject($subject);
        }

        if ($from = $message->getFrom()) {
            $email->from(...$this->createAddresses($from));
        }

        if ($to = $message->getTo()) {
            $email->to(...$this->createAddresses($to));
        }

        if ($replyTo = $message->getReplyTo()) {
            $email->replyTo(...$this->createAddresses($replyTo));
        }

        if ($sender = $message->getSender()) {
            $email->sender($this->createAddress($sender));
        }

        if ($returnPath = $message->getReturnPath()) {
            $email->returnPath($this->createAddress($returnPath));
        }

        if ($cc = $message->getCc()) {
            $email->cc(...$this->createAddresses($cc));
        }

        if ($bcc = $message->getBcc()) {
            $email->bcc(...$this->createAddresses($bcc));
        }

        if ($date = $message->getDate()) {
            $email->date($date);
        }

        if (null !== $priority = $message->getPriority()) {
            $email->priority($priority);
        }

        if ($body = $message->getBody()) {
            $email->html($body, $message->getCharset() ?? 'utf-8');
        }

        foreach ($message->getAttachments() as $attachment) {
            $this->includeAttachment($email, $attachment);
        }

        return $email;
    }

    private function createAddress(Address $address): MimeAddress
    {
        return $this->addressFactory->create($address);
    }

    /**
     * @param  array<Address> $addresses
     *
     * @return array<MimeAddress>
     */
    private function createAddresses(array $addresses): array
    {
        return array_map(
            fn (Address $address) => $this->createAddress($address),
            $addresses,
        );
    }

    private function includeAttachment(Email $email, Attachment $attachment): void
    {
        $file = $this->attachmentProvider->getFile($attachment);

        $path = $file->getPathname();
        $contentType = $attachment->getContentType();

        if (null !== $name = $attachment->getName()) {
            $email->attachFromPath($path, $name, $contentType);
        } elseif (null !== $contentId = $attachment->getContentId()) {
            $email->embedFromPath($path, $contentId, $contentType);
        }
    }
}
