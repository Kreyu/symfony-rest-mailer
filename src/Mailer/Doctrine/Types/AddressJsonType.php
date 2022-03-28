<?php

declare(strict_types=1);

namespace App\Mailer\Doctrine\Types;

use App\Mailer\Entity\ValueObject\Address;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\ConversionException;
use Doctrine\DBAL\Types\JsonType;

class AddressJsonType extends JsonType
{
    /**
     * {@inheritdoc}
     *
     * @throws ConversionException
     */
    public function convertToPHPValue($value, AbstractPlatform $platform)
    {
        $value = parent::convertToPHPValue($value, $platform);

        if ($value === null || $value === '') {
            return null;
        }

        return array_map(
            static function (array $data) {
                $address = new Address();
                $address->setAddress($data['address'] ?? null);
                $address->setName($data['name'] ?? null);

                return $address;
            },
            $value,
        );
    }

    public function getName(): string
    {
        return 'address_json';
    }
}
