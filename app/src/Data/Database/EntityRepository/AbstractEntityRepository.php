<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractEntityRepository extends EntityRepository
{
    /**
     * @var DateTimeImmutable
     */
    protected $currentTime;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * We are away from dependency injection via constructors territory, so we have to rely on the (risky) strategy
     * of having setters for these. Everything is safe and predictable as long as repositories are only EVER called
     * via our custom EntityManager and ALL entities have a repository which extends this class
     */

    public function setCurrentTime(DateTimeImmutable $time): void
    {
        $this->currentTime = $time;
    }

    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    public function setEntityManager(EntityManagerInterface $em): void
    {
        $this->_em = $em;
    }

    public function getByID(
        UuidInterface $uuid,
        $resultType = Query::HYDRATE_ARRAY
    ) {
        $qb = $this->createQueryBuilder('tbl')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid);
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function deleteById(UuidInterface $uuid, string $className): void
    {
        $sql = 'DELETE FROM ' . $className . ' t WHERE t.id = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $uuid);
        $query->execute();
    }
}