<?php

declare(strict_types=1);

namespace App\Mailer\Util;

use App\Mailer\Entity\Attachment;
use App\Mailer\Exception\Base64DecodeException;
use InvalidArgumentException;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\File;

class AttachmentFileManager
{
    public function __construct(
        private string $storageDirectory,
    ) {
        // ...
    }

    public function getFile(Attachment $attachment): File
    {
        return new File($this->getFilename($attachment));
    }

    /**
     * @throws Base64DecodeException
     */
    public function saveFile(Attachment $attachment, string $base64): void
    {
        // Strip "data" part from the base64 string.
        $base64 = preg_replace('#^data:[^;]+;base64,#', '', $base64);

        $content = base64_decode($base64, true);

        if (false === $content) {
            throw new Base64DecodeException();
        }

        $filesystem = new Filesystem();
        $filesystem->dumpFile($this->getFilename($attachment), $content);
    }

    public function getStorageDirectory(): string
    {
        return rtrim($this->storageDirectory, '/');
    }

    private function getFilename(Attachment $attachment): string
    {
        $message = $attachment->getMessage();

        if (null === $message) {
            throw new InvalidArgumentException('Given attachment has no related message.');
        }

        $project = $message->getProject();

        if (null === $project) {
            throw new InvalidArgumentException('Given attachment\'s message has no related project.');
        }

        return sprintf(
            '%s/%s/%s/%s',
            $this->getStorageDirectory(),
            $project->getUuid(),
            $message->getUuid(),
            $attachment->getUuid(),
        );
    }
}
