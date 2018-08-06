<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use App\Data\Database\Entity\KeyValue;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

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

    public function updateEntry(
        UuidInterface $id,
        string $description,
        string $value
    ): void {
        $sql = 'UPDATE ' . KeyValue::class . ' t 
            SET t.description = :description,
                t.value = :value
            WHERE t.id = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $id->getBytes())
            ->setParameter('description', $description)
            ->setParameter('value', $value);
        $query->execute();
    }
}
