<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\Person;
use Doctrine\ORM\Query;

class PersonRepository extends AbstractEntityRepository
{
    public function findAll(
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.name', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllByCommittee(
        bool $onCommittee,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.isOnCommittee = :isOnCommittee')
            ->orderBy('tbl.committeeOrder', 'ASC')
            ->addOrderBy('tbl.name', 'ASC')
            ->setParameter('isOnCommittee', $onCommittee);
        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteByLegacyId(int $legacyId): void
    {
        $sql = 'DELETE FROM ' . Person::class . ' t WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId);
        $query->execute();
    }

    public function findByLegacyId(int $legacyId, int $resultType = Query::HYDRATE_ARRAY)
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.pkid = :legacyId')
            ->setParameter('legacyId', $legacyId);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }
}
