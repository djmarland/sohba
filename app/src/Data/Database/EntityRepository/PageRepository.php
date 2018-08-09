<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\Page;
use App\Data\ID;
use Doctrine\ORM\Query;

class PageRepository extends AbstractEntityRepository
{
    public function findAllInCategories(
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'category')
            ->innerJoin('tbl.category', 'category')
            ->orderBy('category.order', 'ASC')
            ->addOrderBy('tbl.order', 'ASC');
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

    public function findAllUncategorised(int $resultType = Query::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.category IS NULL')
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
