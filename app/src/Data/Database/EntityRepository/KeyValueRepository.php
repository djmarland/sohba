<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\KeyValue;
use Doctrine\ORM\Query;

class KeyValueRepository extends AbstractEntityRepository
{
    public function findAll(
        int $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl')
            ->orderBy('tbl.key', 'ASC');
        return $qb->getQuery()->getResult($resultType);
    }

    public function deleteKeys(array $extraKeys): void
    {
        $sql = 'DELETE FROM ' . KeyValue::class . ' t WHERE t.key IN (:keys)';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('keys', $extraKeys);
        $query->execute();
    }
}
