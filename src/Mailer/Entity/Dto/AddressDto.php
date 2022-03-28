<?php

declare(strict_types=1);

namespace App\Mailer\Entity\Dto;

use Symfony\Component\Validator\Constraints;

class AddressDto
{
    #[Constraints\NotBlank]
    #[Constraints\Email]
    public string $address;
    public ?string $name = null;
}
