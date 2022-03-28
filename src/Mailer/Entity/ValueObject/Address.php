<?php

declare(strict_types=1);

namespace App\Mailer\Entity\ValueObject;

use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Embeddable]
class Address implements JsonSerializable
{
    #[ORM\Column(type: "string", nullable: true)]
    private ?string $address;

    #[ORM\Column(type: "string", nullable: true)]
    private ?string $name;

    public function __construct(?string $address = null, ?string $name = null)
    {
        $this->address = $address;
        $this->name = $name;
    }

    public function __toString(): string
    {
        if ($this->name) {
            return sprintf('[%s] %s', $this->name, $this->address);
        }

        return $this->address;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): void
    {
        $this->address = $address;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'name' => $this->name,
        ];
    }
}