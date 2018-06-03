<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\PageCategory;
use Doctrine\ORM\Query;

class PageCategoryRepository extends AbstractEntityRepository
{
    public function findAllOrdered(
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.order', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findByLegacyId(
        int $legacyId,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.pkid = :legacyId')
            ->setParameter('legacyId', $legacyId);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function deleteByLegacyId(int $legacyId): void
    {
        $sql = 'DELETE FROM ' . PageCategory::class . ' t WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId);
        $query->execute();
    }

    public function updateCategoryPosition(int $catId, int $position): void
    {
        $sql = 'UPDATE ' . PageCategory::class . ' t SET t.order = :newPosition WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $catId)
            ->setParameter('newPosition', $position);
        $query->execute();
    }
}
