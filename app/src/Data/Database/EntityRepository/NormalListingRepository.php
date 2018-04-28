<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

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
            ->orderBy('tbl.timeInt', 'ASC')
            ->setParameter('day', $day);

        return $qb->getQuery()->getResult($resultType);
    }
}
