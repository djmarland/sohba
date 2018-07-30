<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\NormalListing;
use App\Data\ID;
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

    public function findAllForLegacyProgrammeId(
        int $programmeId,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('IDENTITY(tbl.programme) = :id')
            ->orderBy('tbl.day', 'ASC')
            ->addOrderBy('tbl.timeInt', 'ASC')
            ->setParameter('id', $programmeId);

        return $qb->getQuery()->getResult($resultType);
    }

    // todo schema - remove this once migrated
    public function migrateTimes()
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.time IS NULL');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            /** @var NormalListing  $result */
            $timeString = str_pad((string)$result->timeInt, 4, '0', STR_PAD_LEFT);
            $hours = substr($timeString, 0, 2);
            $minutes = substr($timeString, 2, 2);
            $time = \DateTimeImmutable::createFromFormat('H:i', $hours . ':' . $minutes);
            $result->time = $time;
            $this->getEntityManager()->persist($result);
        }
        $this->getEntityManager()->flush();

        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.uuid = :nothing')
            ->setParameter('nothing', '');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            /** @var NormalListing  $result */
            $newId = ID::makeNewID(NormalListing::class);
            $result->id = $newId;
            $result->uuid = (string)$newId;
            $this->getEntityManager()->persist($result);
        }
        $this->getEntityManager()->flush();
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
