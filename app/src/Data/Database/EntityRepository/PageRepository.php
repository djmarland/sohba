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
            ->select('tbl')
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
}
