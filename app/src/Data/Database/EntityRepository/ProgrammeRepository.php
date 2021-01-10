<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\NormalListing;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use Ramsey\Uuid\UuidInterface;

class ProgrammeRepository extends AbstractEntityRepository
{
    /**
     * @return mixed
     */
    public function getByIDWithPeople(
        UuidInterface $uuid,
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'people', 'image')
            ->leftJoin('tbl.people', 'people')
            ->leftJoin('people.image', 'image')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid->getBytes());
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    /**
     * @return mixed
     */
    public function getByIdWithImage(
        UuidInterface $uuid,
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid->getBytes());
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findAll(
        int $resultType = Query::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findAllActive(
        int $resultType = Query::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->innerJoin(NormalListing::class, 'nl', Join::WITH, 'nl.programme = tbl')
            ->distinct()
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    /**
     * @return mixed
     */
    public function findByLegacyId(
        int $id,
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'image')
            ->leftJoin('tbl.image', 'image')
            ->where('tbl.pkid = :id')
            ->setParameter('id', $id);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function findByTypes(
        array $typeIds,
        int $resultType = Query::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.type IN (:types)')
            ->addOrderBy('tbl.type', 'ASC')
            ->addOrderBy('tbl.title', 'ASC')
            ->setParameter('types', $typeIds);
        return $qb->getQuery()->getResult($resultType);
    }

    public function getProgrammesWithPeople(
        int $resultType = Query::HYDRATE_ARRAY
    ): array {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'people')
            ->innerJoin('tbl.people', 'people');
        return $qb->getQuery()->getResult($resultType);
    }
}
