<?php

declare(strict_types=1);

namespace App\Mailer\Mime\Factory;

use App\Mailer\Entity\ValueObject\Address;
use Symfony\Component\Mime\Address as MimeAddress;

class AddressFactory
{
    public function create(Address $address): MimeAddress
    {
        return new MimeAddress($address->getAddress(), $address->getName() ?? '');
    }
}
