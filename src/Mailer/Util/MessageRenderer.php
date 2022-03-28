<?php

declare(strict_types=1);

namespace App\Mailer\Util;

use App\Mailer\Entity\Attachment;
use App\Mailer\Entity\Message;

class MessageRenderer
{
    public function __construct(
        private AttachmentFileManager $attachmentFileManager,
    ) {
        // ...
    }

    /**
     * This method will return given message body.
     * You can use this to render the message straight in the browser.
     *
     * Every embedded image will be converted from contentId to base64.
     * For example, when given message body equals:
     *
     * <pre>
     *  <img src="cid:test123"/>
     * </pre>
     *
     * and message contains attachment with `contentId = test123`, returned string will equal:
     *
     * <pre>
     *  <img src="data:image/png;base64,iVBORw0KGgoAA..."/>
     * </pre>
     */
    public function render(Message $message): string
    {
        $body = $message->getBody();

        preg_match_all('@cid:([^"]+)@' , $body, $contentIds);

        /**
         * Following the example message body:
         *
         * <pre>
         *  <img src="cid:test123"/>
         * </pre>
         *
         * Match array $contentIds equals:
         *
         * <pre>
         * [
         *     0 => [
         *         "cid:test123",
         *     ],
         *     1 => [
         *         "test123",
         *     ],
         * ]
         * </pre>
         *
         * $contentIds[0] => strings that full matched, e.g. cid:test123
         * $contentIds[1] => strings enclosed by tags, e.g. test123
         */

        [$contentIdsWithCid, $contentIdsWithoutCid] = $contentIds;

        foreach ($contentIdsWithoutCid as $index => $contentId) {
            /** @var Attachment $attachment */
            foreach ($message->getAttachments() as $attachment) {
                if ($attachment->getContentId() !== $contentId) {
                    continue;
                }

                $file = $this->attachmentFileManager->getFile($attachment);

                $mimeType = $attachment->getContentType() ?? $file->getMimeType();
                $base64 = base64_encode($file->getContent());

                $imageSource = sprintf('data:%s;base64,%s', $mimeType, $base64);

                $body = str_replace($contentIdsWithCid[$index], $imageSource, $body);
            }
        }

        return $body;
    }
}