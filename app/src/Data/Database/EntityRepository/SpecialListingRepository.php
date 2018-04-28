<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use Doctrine\ORM\Query;

class SpecialListingRepository extends AbstractEntityRepository
{
    public function findAllForLegacySpecialDayId(
        int $specialDayId,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('IDENTITY(tbl.specialDay) = :specialDayId')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.timeInt', 'ASC')
            ->setParameter('specialDayId', $specialDayId);
        return $qb->getQuery()->getResult($resultType);
    }
}
