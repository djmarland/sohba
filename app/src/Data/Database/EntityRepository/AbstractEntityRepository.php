<?php
declare(strict_types=1);

namespace App\Data\Database\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Psr\Log\LoggerInterface;
use Ramsey\Uuid\UuidInterface;

abstract class AbstractEntityRepository extends EntityRepository
{
    /**
     * @var DateTimeImmutable
     */
    protected DateTimeImmutable $currentTime;

    /**
     * @var LoggerInterface
     */
    protected LoggerInterface $logger;

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
        int $resultType = AbstractQuery::HYDRATE_ARRAY
    ): mixed {
        $qb = $this->createQueryBuilder('tbl')
            ->where('tbl.id = :id')
            ->setParameter('id', $uuid->getBytes());
        return $qb->getQuery()->getOneOrNullResult($resultType);
    }

    public function deleteById(UuidInterface $uuid, string $className): void
    {
        $sql = 'DELETE FROM ' . $className . ' t WHERE t.id = :id';
        $query = $this->getEntityManager()
            ->createQuery($sql)
            ->setParameter('id', $uuid->getBytes());
        $query->execute();
    }
}
