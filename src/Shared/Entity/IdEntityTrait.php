<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;

trait IdEntityTrait
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer", unique: true)]
    #[ApiPlatform\ApiProperty(identifier: false)]
    private ?int $id = null;

    public function getId(): null|int|string
    {
        return $this->id;
    }
}