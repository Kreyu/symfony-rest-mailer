<?php

declare(strict_types=1);

namespace App\Shared\Entity;

use ApiPlatform\Core\Annotation as ApiPlatform;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Uid\Uuid;

trait UuidEntityTrait
{
    #[ORM\Column(type: "uuid", unique: true)]
    #[ApiPlatform\ApiProperty(identifier: true)]
    private Uuid $uuid;

    protected function initializeUuid(): void
    {
        $this->uuid = Uuid::v4();
    }

    public function getUuid(): Uuid
    {
        return $this->uuid;
    }
}