<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\NormalListing;
use Doctrine\ORM\Query;

class NormalListingRepository extends AbstractEntityRepository
{
    public function findAllForDay(
        int $day,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('tbl.day = :day')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.time', 'ASC')
            ->setParameter('day', $day);

        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllForLegacyProgrammeId(
        int $programmeId,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('IDENTITY(tbl.programme) = :id')
            ->orderBy('tbl.day', 'ASC')
            ->addOrderBy('tbl.time', 'ASC')
            ->setParameter('id', $programmeId);

        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteAllForDay(int $day): void
    {
        $sql = 'DELETE FROM ' . NormalListing::class . ' t WHERE t.day = :day';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('day', $day);
        $query->execute();
    }
}
