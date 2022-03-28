<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Factory;

use App\Mailer\Entity\Transaction;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Messenger\Stamp\ErrorDetailsStamp;

class TransactionFactory
{
    public function createFromSentMessage(SentMessage $sentMessage): Transaction
    {
        $transaction = new Transaction();
        $transaction->setTransportMessageId($sentMessage->getMessageId());
        $transaction->setTransportDebug($sentMessage->getDebug());

        return $transaction;
    }

    public function createFromErrorDetailsStamp(ErrorDetailsStamp $stamp): Transaction
    {
        $transaction = new Transaction();
        $transaction->setExceptionCode($stamp->getExceptionCode());
        $transaction->setExceptionClass($stamp->getExceptionClass());
        $transaction->setExceptionMessage($stamp->getExceptionMessage());

        return $transaction;
    }
}