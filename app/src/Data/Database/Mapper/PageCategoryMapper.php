<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\PageCategory;

class PageCategoryMapper implements MapperInterface
{
    public function map(array $item): PageCategory
    {
        return new PageCategory(
            $item['id'],
            $item['title']
        );
    }
}
