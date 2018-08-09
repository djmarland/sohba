<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Page;
use App\Data\Database\Entity\PageCategory as DbPageCategory;
use App\Data\Database\Entity\Page as DbPage;
use App\Domain\Entity\PageCategory;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

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
            $title,
            9999
        );
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function updateCategoryPosition(int $catId, int $position): void
    {
        $this->entityManager->getPageCategoryRepo()->updateCategoryPositionByLegacyId($catId, $position);
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
        string $htmlContent,
        ?int $navPosition,
        $navCategoryId = null // todo - type hint for UUID
    ): void {
        /** @var DbPage $entity */
        $entity = $this->entityManager->getPersonRepo()->getByID(
            $page->getId(),
            Query::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new \InvalidArgumentException('Tried to update a page that does not exist');
        }

        // todo - remove the int support
        if ($navCategoryId instanceof UuidInterface) {
            $category = $this->entityManager->getPageCategoryRepo()->getByID(
                $navCategoryId,
                Query::HYDRATE_OBJECT
            );
        } elseif (\is_int($navCategoryId)) {
            // todo - remove this bit
            $category = $this->entityManager->getPageCategoryRepo()->findByLegacyId(
                $navCategoryId,
                Query::HYDRATE_OBJECT
            );
        } else {
            $category = null;
        }

        $entity->title = $title;
        $entity->urlPath = $url;
        $entity->htmlContent = $htmlContent;
        $entity->order = $navPosition;
        $entity->category = $category;

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
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
            $title,
            $url,
            '',
            0
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
    }
}
