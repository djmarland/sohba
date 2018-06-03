<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\ID;
use App\Domain\Entity\Page;
use App\Data\Database\Entity\PageCategory as DbPageCategory;
use Doctrine\ORM\Query;

class PageService extends AbstractService
{
    public function findByLegacyId(int $legacyId): ?Page
    {
        return $this->mapSingle(
            $this->entityManager->getPageRepo()->findByLegacyId($legacyId),
            $this->pageMapper
        );
    }

    public function findByUrl(string $urlPath): ?Page
    {
        return $this->mapSingle(
            $this->entityManager->getPageRepo()->findByUrlPath($urlPath),
            $this->pageMapper
        );
    }

    public function findAllForNavigation(): array
    {
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllInCategories(),
            $this->pageMapper
        );
    }

    public function findAllPageCategories(): array
    {
        return $this->mapMany(
            $this->entityManager->getPageCategoryRepo()->findAllOrdered(),
            $this->pageCategoryMapper
        );
    }

    public function updatePageCategoryTitle(int $legacyId, string $newTitle): void
    {
        $category = $this->entityManager->getPageCategoryRepo()
            ->findByLegacyId($legacyId, Query::HYDRATE_OBJECT);

        /** @var \App\Data\Database\Entity\PageCategory $category */
        $category->title = $newTitle;
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function deletePageCategory(int $categoryId): void
    {
        $this->entityManager->getPageCategoryRepo()->deleteByLegacyId($categoryId);
    }

    public function newPageCategory(string $title)
    {
        $category = new DbPageCategory(
            ID::makeNewID(DbPageCategory::class),
            $title,
            9999
        );
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function updateCategoryPosition(int $catId, int $position): void
    {
        $this->entityManager->getPageCategoryRepo()->updateCategoryPosition($catId, $position);
    }
}
