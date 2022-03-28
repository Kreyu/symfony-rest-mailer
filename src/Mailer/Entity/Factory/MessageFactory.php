<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Factory;

use App\Mailer\Entity\Dto\MessageDto;
use App\Mailer\Entity\Message;
use App\Mailer\Exception\Base64DecodeException;
use App\Mailer\Util\AttachmentFileManager;

class MessageFactory
{
    public function __construct(
        private AddressFactory $addressFactory,
        private AttachmentFactory $attachmentFactory,
        private AttachmentFileManager $attachmentFileManager,
    ) {
        // ...
    }

    /**
     * @throws Base64DecodeException
     */
    public function createFromDto(MessageDto $dto): Message
    {
        $message = new Message();
        $message->setSubject($dto->subject);

        foreach ($dto->from as $addressDto) {
            $message->addFrom($this->addressFactory->createFromDto($addressDto));
        }

        foreach ($dto->from as $addressDto) {
            $message->addTo($this->addressFactory->createFromDto($addressDto));
        }

        foreach ($dto->from as $addressDto) {
            $message->addCc($this->addressFactory->createFromDto($addressDto));
        }

        foreach ($dto->from as $addressDto) {
            $message->addBcc($this->addressFactory->createFromDto($addressDto));
        }

        foreach ($dto->from as $addressDto) {
            $message->addReplyTo($this->addressFactory->createFromDto($addressDto));
        }

        if ($dto->sender) {
            $message->setSender($this->addressFactory->createFromDto($dto->sender));
        }

        if ($dto->returnPath) {
            $message->setReturnPath($this->addressFactory->createFromDto($dto->returnPath));
        }

        $message->setBody($dto->body);
        $message->setCharset($dto->charset);
        $message->setDate($dto->date);
        $message->setPriority($dto->priority);

        foreach ($dto->attachments as $attachmentDto) {
            $attachment = $this->attachmentFactory->createFromDto($attachmentDto);
            $attachment->setMessage($message);

            $this->attachmentFileManager->saveFile($attachment, $attachmentDto->base64);

            $message->addAttachment($attachment);
        }

        return $message;
    }
}