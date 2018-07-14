<?php
declare(strict_types=1);

namespace App\Data\Database\Mapper;

use App\Domain\Entity\Null\NullPageCategory;
use App\Domain\Entity\Page;
use App\Domain\ValueObject\RichText;

class PageMapper implements MapperInterface
{
    private $pageCategoryMapper;

    public function __construct(
        PageCategoryMapper $pageCategoryMapper
    ) {
        $this->pageCategoryMapper = $pageCategoryMapper;
    }

    public function map(array $item): Page
    {
        $html = !empty($item['htmlContent']) ? new RichText($item['htmlContent']) : null;

        return new Page(
            $item['id'],
            $item['pkid'],
            $item['title'],
            $item['content'],
            $html,
            $item['urlPath'],
            $item['order'],
            $this->mapCategory($item)
        );
    }

    private function mapCategory(array $item)
    {
        if (array_key_exists('category', $item)) {
            if (isset($item['category'])) {
                return $this->pageCategoryMapper->map($item['category']);
            }
            return new NullPageCategory();
        }
        return null;
    }
}
