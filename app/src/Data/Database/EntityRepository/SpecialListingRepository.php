<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\SpecialListing;
use App\Data\ID;
use DateTimeImmutable;
use DateTimeInterface;
use Doctrine\ORM\Query;

class SpecialListingRepository extends AbstractEntityRepository
{
    public function findListingsOfTypesAfter(
        array $programmeTypes,
        DateTimeImmutable $after,
        int $limit = null,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme')
            ->innerJoin('tbl.programme', 'programme')
            ->where('tbl.dateTimeUk >= :after')
            ->andWhere('programme.type IN (:types)')
            ->orderBy('tbl.dateTimeUk', 'ASC')
            ->setParameter('after', $after)
            ->setParameter('types', $programmeTypes);

        if ($limit) {
            $qb->setMaxResults($limit);
        }

        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllForDate(
        DateTimeImmutable $specialDate,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'image')
            ->where('tbl.dateUk = :dateUk')
            ->innerJoin('tbl.programme', 'programme')
            ->leftJoin('programme.image', 'image')
            ->orderBy('tbl.dateTimeUk', 'ASC')
            ->setParameter('dateUk', $specialDate);
        return $qb->getQuery()->getResult($resultType);
    }

    public function findNextForLegacyProgrammeId(
        int $getLegacyId,
        DateTimeImmutable $now,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.dateUk >= :after')
            ->andWhere('IDENTITY(tbl.programme) = :programmeId')
            ->setMaxResults(1)
            ->setParameter('after', $now)
            ->setParameter('programmeId', $getLegacyId);

        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findDates(
        ?DateTimeInterface $from = null,
        ?DateTimeInterface $to = null
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('DISTINCT(tbl.dateUk)')
            ->orderBy('tbl.dateUk', 'ASC');

        if ($from) {
            $qb = $qb->andWhere('tbl.dateUk >= :from')
                ->setParameter('from', $from);
        }
        if ($to) {
            $qb = $qb->andWhere('tbl.dateUk < :to')
                ->setParameter('to', $to);
        }

        return array_map('reset', $qb->getQuery()->getResult(Query::HYDRATE_ARRAY));
    }

    public function migrate(): void
    {

        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'date')
            ->leftJoin('tbl.specialDay', 'date')
            ->where('tbl.dateTimeUk IS NULL');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            if (!$result->specialDay) {
                $this->getEntityManager()->remove($result);
                continue;
            }

            $time = str_pad((string)$result->timeInt, 4, '0', STR_PAD_LEFT);
            $date = str_pad((string)$result->specialDay->dateInt, 8, '0', STR_PAD_LEFT);


            $dateTime = DateTimeImmutable::createFromFormat(
                'dmY-Hi',
                $date . '-' . $time
            );

            $result->dateTimeUk = $dateTime;
            $result->dateUk = $dateTime;
            $this->getEntityManager()->persist($result);
        }


        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.uuid = :nothing')
            ->setParameter('nothing', '');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            /** @var SpecialListing  $result */
            $newId = ID::makeNewID(SpecialListing::class);
            $result->id = $newId;
            $result->uuid = (string)$newId;
            $result->createdAt = new DateTimeImmutable('2017-01-01T00:00:00Z');
            $result->updatedAt = new DateTimeImmutable('2017-01-01T00:00:00Z');
            $this->getEntityManager()->persist($result);
        }
        $this->getEntityManager()->flush();
    }

    public function deleteBetween(DateTimeImmutable $fromInclusive, DateTimeImmutable $toExclusive): void
    {
        $sql = 'DELETE FROM ' . SpecialListing::class . ' t WHERE t.dateTimeUk >= :from AND t.dateTimeUk < :to';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('from', $fromInclusive)
            ->setParameter('to', $toExclusive);
        $query->execute();
    }
}
