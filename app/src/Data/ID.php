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

    public static function makeNewID(string $entityClass): UuidInterface
    {
        if (!isset(self::ENTITY_MAPPINGS[$entityClass])) {
            throw new InvalidArgumentException($entityClass . ' not in the list of entity mappings');
        }

        return self::markUuid(Uuid::uuid4(), $entityClass);
    }

    private static function markUuid(UuidInterface $uuid, string $entityClass): UuidInterface
    {
        $str = (string)$uuid;
        $str = substr_replace($str, self::ENTITY_MAPPINGS[$entityClass], 9, 4);
        return Uuid::fromString($str);
    }
}
