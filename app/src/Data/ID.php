<?php
declare(strict_types=1);

namespace App\Data;

use App\Data\Database\Entity\Image;
use App\Data\Database\Entity\KeyValue;
use App\Data\Database\Entity\NormalListing;
use App\Data\Database\Entity\Page;
use App\Data\Database\Entity\PageCategory;
use App\Data\Database\Entity\Person;
use App\Data\Database\Entity\Programme;
use App\Data\Database\Entity\SpecialListing;
use InvalidArgumentException;
use Ramsey\Uuid\Uuid;
use Ramsey\Uuid\UuidInterface;

class ID
{
    private const ENTITY_MAPPINGS = [
        Image::class => '1111',
        NormalListing::class => '0000',
        Page::class => 'a0a0',
        PageCategory::class => 'ca0e',
        Person::class => '0666',
        Programme::class => 'edfa',
        SpecialListing::class => 'face',
        KeyValue::class => 'c0c0',
    ];

    private const NAMESPACE = '00000000-0000-0000-0000-000000000000';

    public static function makeNewID(string $entityClass): UuidInterface
    {
        if (!isset(self::ENTITY_MAPPINGS[$entityClass])) {
            throw new InvalidArgumentException($entityClass . ' not in the list of entity mappings');
        }

        return self::markUuid(Uuid::uuid4(), $entityClass);
    }

    public static function makeIDFromKey(string $entityClass, string $key): UuidInterface
    {
        $uuid = Uuid::uuid5(self::NAMESPACE, sha1($key));
        return self::markUuid($uuid, $entityClass);
    }

    public static function getIDType(UuidInterface $id): string
    {
        $part = (string)substr((string)$id, 9, 4);
        $map = array_flip(self::ENTITY_MAPPINGS);
        if ($map[$part]) {
            return $map[$part];
        }
        throw new InvalidArgumentException('Id not recognised');
    }

    private static function markUuid(UuidInterface $uuid, string $entityClass): UuidInterface
    {
        $str = (string)$uuid;
        $str = substr_replace($str, self::ENTITY_MAPPINGS[$entityClass], 9, 4);
        return Uuid::fromString($str);
    }
}
