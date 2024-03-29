<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\PageCategory;
use Doctrine\ORM\AbstractQuery;
use Ramsey\Uuid\UuidInterface;

class PageRepository extends AbstractEntityRepository
{
    public function getByIdWithCategory(
        UuidInterface $uuid,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): mixed {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'category')
            ->leftJoin('tbl.category', 'category')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid->getBytes());
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAllInCategories(
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'category')
            ->innerJoin('tbl.category', 'category')
            ->orderBy('category.order', 'ASC')
            ->addOrderBy('tbl.order', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findByUrlPath(string $urlPath, int $resultType = AbstractQuery::HYDRATE_ARRAY): mixed
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.urlPath = :path')
            ->setParameter('path', $urlPath);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAllInCategory(PageCategory $category, int $resultType = AbstractQuery::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.category = :category')
            ->orderBy('tbl.order', 'ASC')
            ->setParameter('category', $category);
        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllUncategorised(int $resultType = AbstractQuery::HYDRATE_ARRAY): array
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.category IS NULL')
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }
}
