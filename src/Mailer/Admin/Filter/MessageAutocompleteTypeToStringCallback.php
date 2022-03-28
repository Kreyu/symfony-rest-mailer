<?php

declare(strict_types=1);

namespace App\Mailer\Admin\Filter;

use App\Mailer\Entity\Message;
use Sonata\DoctrineORMAdminBundle\Filter\ModelAutocompleteFilter;
use Symfony\Component\String\UnicodeString;

class MessageAutocompleteTypeToStringCallback
{
    /**
     * This method should be used as a "to_string_callback" field option of the {@see ModelAutocompleteFilter} filter type.
     */
    public static function toString(Message $message): string
    {
        $string = sprintf('#%s (%s)', $message->getId(), $message->getUuid());

        if (null !== $subject = $message->getSubject()) {
            $string .= ' ' . (new UnicodeString($subject))->truncate(120, '...');
        }

        return $string;
    }
}
