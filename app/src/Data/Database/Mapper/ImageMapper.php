<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Image;

class ImageMapper implements MapperInterface
{
    public function map(array $item): Image
    {
        return new Image(
            $item['id'],
            $item['pkid'],
            $item['title'],
            $item['fileName']
        );
    }
}
