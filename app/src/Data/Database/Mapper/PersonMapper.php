<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Image;
use App\Domain\Entity\Null\NullImage;
use App\Domain\Entity\Person;

class PersonMapper implements MapperInterface
{
    private ImageMapper $imageMapper;

    public function __construct(
        ImageMapper $imageMapper
    ) {
        $this->imageMapper = $imageMapper;
    }

    public function map(array $item): Person
    {
        return new Person(
            $item['id'],
            $item['name'],
            $item['isOnCommittee'],
            !empty($item['committeeTitle']) ? $item['committeeTitle'] : null,
            !empty($item['committeeOrder']) ? $item['committeeOrder'] : null,
            $this->mapImage($item)
        );
    }

    private function mapImage(array $item): ?Image
    {
        if (array_key_exists('image', $item)) {
            if (isset($item['image'])) {
                return $this->imageMapper->map($item['image']);
            }
            return new NullImage();
        }
        return null;
    }
}
