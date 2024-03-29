<?php
declare(strict_types=1);

namespace App\Service;

use App\Domain\Entity\Page;
use App\Data\Database\Entity\PageCategory as DbPageCategory;
use App\Data\Database\Entity\Page as DbPage;
use App\Domain\Entity\PageCategory;
use Doctrine\ORM\AbstractQuery;
use InvalidArgumentException;
use Ramsey\Uuid\UuidInterface;
use RuntimeException;

class PageService extends AbstractService
{
    public function findById(UuidInterface $id): ?Page
    {
        return $this->mapSingle(
            $this->entityManager->getPageRepo()->getByIdWithCategory($id),
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

    public function updatePageCategoryTitle(UuidInterface $id, string $newTitle): void
    {
        $category = $this->entityManager->getPageCategoryRepo()
            ->getByID($id, AbstractQuery::HYDRATE_OBJECT);

        /** @var \App\Data\Database\Entity\PageCategory $category */
        $category->title = $newTitle;
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function deletePageCategory(UuidInterface $categoryId): void
    {
        $this->entityManager->getPageCategoryRepo()->deleteById($categoryId, DbPageCategory::class);
    }

    public function deletePage(UuidInterface $pageId): void
    {
        $this->entityManager->getPageRepo()->deleteById($pageId, DbPage::class);
    }

    public function newPageCategory(string $title): void
    {
        $category = new DbPageCategory(
            $title,
            9999
        );
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function updateCategoryPosition(UuidInterface $catId, int $position): void
    {
        $category = $this->entityManager->getPageCategoryRepo()
            ->getByID($catId, AbstractQuery::HYDRATE_OBJECT);

        $category->order = $position;
        $this->entityManager->persist($category);
        $this->entityManager->flush();
    }

    public function findAllInCategory(PageCategory $category): array
    {
        $categoryEntity = $this->entityManager->getPageCategoryRepo()->getByID(
            $category->getId(),
            AbstractQuery::HYDRATE_OBJECT
        );
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllInCategory($categoryEntity),
            $this->pageMapper
        );
    }

    public function updatePage(
        Page $page,
        string $title,
        string $url,
        string $htmlContent,
        ?int $navPosition,
        ?UuidInterface $navCategoryId = null
    ): void {
        /** @var DbPage|null $entity */
        $entity = $this->entityManager->getPageRepo()->getByID(
            $page->getId(),
            AbstractQuery::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new InvalidArgumentException('Tried to update a page that does not exist');
        }

        $category = null;
        if ($navCategoryId) {
            $category = $this->entityManager->getPageCategoryRepo()->getByID(
                $navCategoryId,
                AbstractQuery::HYDRATE_OBJECT
            );
        }

        $entity->title = $title;
        $entity->urlPath = $url;
        $entity->htmlContent = $htmlContent;
        $entity->order = $navPosition ?? 0;
        $entity->category = $category;

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }

    public function findAllUncategorised(): array
    {
        return $this->mapMany(
            $this->entityManager->getPageRepo()->findAllUncategorised(),
            $this->pageMapper
        );
    }

    public function newPage(string $title): UuidInterface
    {
        $url = str_replace(' ', '-', strtolower($title));
        $url = preg_replace('/[^a-z0-9-]/s', '', $url);

        if (!$url) {
            throw new RuntimeException('Could not parse page name');
        }

        $page = new DbPage(
            $title,
            $url,
            '',
            0
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->id;
    }
}
