<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Factory;

use App\Mailer\Entity\Dto\AddressDto;
use App\Mailer\Entity\ValueObject\Address;

class AddressFactory
{
    public function createFromDto(AddressDto $dto): Address
    {
        return new Address(
            address: $dto->address,
            name:    $dto->name,
        );
    }
}