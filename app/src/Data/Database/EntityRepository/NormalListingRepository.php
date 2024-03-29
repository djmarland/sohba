<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\NormalListing;
use App\Data\Database\Entity\Programme;
use Doctrine\ORM\AbstractQuery;

class NormalListingRepository extends AbstractEntityRepository
{
    public function findAllForDay(
        int $day,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('tbl.day = :day')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.time', 'ASC')
            ->setParameter('day', $day);

        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllForProgramme(
        Programme $programme,
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.programme = :programme')
            ->orderBy('tbl.day', 'ASC')
            ->addOrderBy('tbl.time', 'ASC')
            ->setParameter('programme', $programme);

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
