<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\ID;
use App\Domain\Entity\Page;
use App\Data\Database\Entity\PageCategory as DbPageCategory;
use App\Data\Database\Entity\Page as DbPage;
use App\Domain\Entity\PageCategory;
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

    public function deletePage(int $pageId): void
    {
        $this->entityManager->getPageRepo()->deleteByLegacyId($pageId);
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

    public function findAllInCategory(PageCategory $category): array
    {
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllInCategoryId($category->getLegacyId()),
            $this->pageMapper
        );
    }

    public function updatePage(
        Page $page,
        string $title,
        string $url,
        string $content,
        ?int $navPosition,
        ?int $navCategoryId
    ): void {
        $this->entityManager->getPageRepo()->updatePage(
            $page->getLegacyId(),
            $title,
            $url,
            $content,
            $navPosition,
            $navCategoryId
        );
    }

    public function findAllUncategorised()
    {
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllUncategorised(),
            $this->pageMapper
        );
    }

    public function newPage(string $title)
    {
        $url = str_replace(' ', '-', strtolower($title));
        $url = preg_replace('/[^a-z0-9-]/s', '', $url);

        $page = new DbPage(
            ID::makeNewID(DbPage::class),
            $title,
            0
        );

        // todo - things that can't be null should be in the constructor (e.g. content)
        $page->urlPath = $url;
        $page->content = '';
        $page->isPublished = true;

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
    }
}
