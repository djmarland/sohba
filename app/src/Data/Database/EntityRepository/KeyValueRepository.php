<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

class KeyValueRepository extends AbstractEntityRepository
{
    public function findValueByKey(string $key)
    {
        $qb = $this->createQueryBuilder('tbl')
            ->select('tbl.value')
            ->where('tbl.key = :key')
            ->setParameter('key', $key);

        return $qb->getQuery()->getSingleScalarResult();
    }
}
