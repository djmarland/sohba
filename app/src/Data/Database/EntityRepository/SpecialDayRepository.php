<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use DateTimeInterface;
use Doctrine\ORM\Query;

class SpecialDayRepository extends AbstractEntityRepository
{
    public function findForDate(
        DateTimeInterface $dateTime,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        // todo - store a real date object in the database rather than an int
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.dateInt = :value')
            ->setParameter('value', $this->dateToLegacyInt($dateTime));
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAllAfterDate(
        DateTimeInterface $dateTime,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        // todo - store a real date object in the database rather than using timestamp
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.timestamp > :timestamp')
            ->orderBy('tbl.timestamp', 'ASC')
            ->setParameter('timestamp', $dateTime->getTimestamp());
        return $qb->getQuery()->getResult($resultType);
    }

    private function dateToLegacyInt(DateTimeInterface $date): int
    {
        return (int) $date->format('jmY');
    }
}
