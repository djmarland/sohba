<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\PersonInShow;
use App\Data\Database\Entity\Programme;
use Doctrine\ORM\Query;

/**
 * @deprecated use the ManyToMany map
 */
class PersonInShowRepository extends AbstractEntityRepository
{
    public function findAll(
        $people = [],
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'programme', 'person')
            ->innerJoin('tbl.programme', 'programme')
            ->innerJoin('tbl.person', 'person')
            ->orderBy('programme.title', 'ASC');

        if (!empty($people)) {
            $qb->where('tbl.person IN (:people)');
            $qb->setParameter('people', $people);
        }

        return $qb->getQuery()->getResult($resultType);
    }

    /**
     * @deprecated - use the new relationship type
     */
    public function findPeopleForProgramme(
        Programme $programme,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl', 'person', 'image')
            ->innerJoin('tbl.person', 'person')
            ->leftJoin('person.image', 'image')
            ->where('tbl.programme = :programme')
            ->orderBy('person.name', 'ASC')
            ->setParameter('programme', $programme);

        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteAllForProgramme(Programme $programme): void
    {
        $sql = 'DELETE FROM ' . PersonInShow::class . ' t WHERE t.programme = :programme';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('programme', $programme);
        $query->execute();
    }
}
