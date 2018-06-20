<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\Page;
use Doctrine\ORM\Query;

class PageRepository extends AbstractEntityRepository
{
    public function findAllInCategories(
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'category')
            ->innerJoin('tbl.category', 'category')
            ->where('tbl.isPublished = :published')
            ->orderBy('category.order', 'ASC')
            ->addOrderBy('tbl.order', 'ASC')
            ->setParameter('published', true);
        return $qb->getQuery()->getResult($resultType);
    }

    public function findByLegacyId(int $legacyId, int $resultType = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'category')
            ->leftJoin('tbl.category', 'category')
            ->where('tbl.pkid = :legacyId')
            ->setParameter('legacyId', $legacyId);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findByUrlPath(string $urlPath, int $resultType = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.urlPath = :path')
            ->setParameter('path', $urlPath);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAllInCategoryId(int $categoryId, int $resultType = Query::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('IDENTITY(tbl.category) = :categoryId')
            ->orderBy('tbl.order', 'ASC')
            ->setParameter('categoryId', $categoryId);
        return $qb->getQuery()->getResult($resultType);
    }

    // todo - this shouldn't exist. Do it properly with entities
    public function updatePage(
        int $legacyId,
        string $title,
        string $url,
        string $legacyContent,
        ?string $htmlContent,
        ?int $navPosition,
        ?int $navCategoryId
    ): void {
        $sql = 'UPDATE ' . Page::class . ' t 
            SET t.title = :title,
                t.urlPath = :url,
                t.content = :content,
                t.htmlContent = :htmlContent,
                t.order = :order,
                t.category = :category
            WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId)
            ->setParameter('title', $title)
            ->setParameter('url', $url)
            ->setParameter('content', $legacyContent)
            ->setParameter('htmlContent', $htmlContent)
            ->setParameter('order', $navPosition)
            ->setParameter('category', $navCategoryId);
        $query->execute();
    }

    public function findAllUncategorised(int $resultType = Query::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.category IS NULL')
            ->orWhere('tbl.category = 0')// todo - always use NULL, not 0
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function newPage($title, $url)
    {
    }

    public function deleteByLegacyId(int $legacyId): void
    {
        $sql = 'DELETE FROM ' . Page::class . ' t WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId);
        $query->execute();
    }
}
