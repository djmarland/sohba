<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\NormalListing;
use App\Data\Database\Entity\Programme;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

class ProgrammeRepository extends AbstractEntityRepository
{
    public function findAllActive(
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->innerJoin(NormalListing::class, 'nl', Join::WITH, 'nl.programme = tbl')
            ->distinct()
            ->orderBy('tbl.title', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function findByLegacyId(
        int $id,
        $resultType = Query::HYDRATE_ARRAY
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
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.type IN (:types)')
            ->addOrderBy('tbl.type', 'ASC')
            ->addOrderBy('tbl.title', 'ASC')
            ->setParameter('types', $typeIds);
        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteByLegacyId(int $legacyId): void
    {
        $sql = 'DELETE FROM ' . Programme::class . ' t WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId);
        $query->execute();
    }

    public function updateProgramme(
        int $legacyId,
        string $name,
        string $tagLine,
        int $type,
        string $detail,
        ?int $imageId
    ): void {
        $sql = 'UPDATE ' . Programme::class . ' t 
            SET t.title = :name,
                t.tagline = :tagLine,
                t.type = :type,
                t.description = :detail,
                t.image = :imageId
            WHERE t.pkid = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $legacyId)
            ->setParameter('name', $name)
            ->setParameter('type', $type)
            ->setParameter('detail', $detail)
            ->setParameter('tagLine', $tagLine)
            ->setParameter('imageId', $imageId)
        ;
        $query->execute();
    }
}
