<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Null\NullImage;
use App\Domain\Entity\Programme;

class ProgrammeMapper implements MapperInterface
{
    private $imageMapper;

    public function __construct(
        ImageMapper $imageMapper
    ) {
        $this->imageMapper = $imageMapper;
    }

    public function map(array $item): Programme
    {
        return new Programme(
            $item['id'],
            $item['pkid'],
            $item['title'],
            $item['type'],
            !empty($item['tagline']) ? $item['tagline'] : null,
            !empty($item['description']) ? $item['description'] : null,
            $this->mapImage($item)
        );
    }

    private function mapImage(array $item)
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
