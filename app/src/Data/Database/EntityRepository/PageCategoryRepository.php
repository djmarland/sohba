<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\PageCategory;
use App\Data\ID;
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

    public function updateCategoryPositionByLegacyId(int $catId, int $position): void
    {
        /** @var PageCategory $entity */
        $entity = $this->findOneBy(['pkid' => $catId]);
        $entity->order = $position;
        $this->getEntityManager()->persist($entity);
        $this->getEntityManager()->flush();
    }

    public function migrate(): void
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')

            ->where('tbl.uuid = :nothing')
            ->setParameter('nothing', '');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            /** @var PageCategory  $result */
            $newId = ID::makeNewID(PageCategory::class);
            $result->id = $newId;
            $result->uuid = (string)$newId;
            $result->createdAt = new \DateTimeImmutable('2017-01-01T00:00:00Z');
            $result->updatedAt = new \DateTimeImmutable('2017-01-01T00:00:00Z');
            $this->getEntityManager()->persist($result);
        }
        $this->getEntityManager()->flush();
    }
}
