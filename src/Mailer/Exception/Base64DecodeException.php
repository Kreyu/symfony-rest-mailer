<?php

declare(strict_types=1);

namespace App\Mailer\Exception;

class Base64DecodeException extends \Exception
{
    public function __construct()
    {
        parent::__construct('Unable to decode base64 string.');
    }
}