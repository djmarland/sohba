<?php
declare(strict_types=1);

namespace App\Data\Database;

use App\Data\Database\Entity\AbstractEntity;
use App\Data\Database\EntityRepository\AbstractEntityRepository;
use DateTimeImmutable;
use Doctrine\ORM\Decorator\EntityManagerDecorator;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

class EntityManager extends EntityManagerDecorator
{
    private DateTimeImmutable $currentTime;
    private LoggerInterface $logger;

    private array $classCache = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        DateTimeImmutable $currentTime,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager);
        $this->currentTime = $currentTime;
        $this->logger = $logger;
    }

    public function persist($entity): void
    {
        /** @var AbstractEntity $entity */
        // interject to update the created_at/updated_at fields (for audit purposes)
        if (!$entity->createdAt) {
            $entity->createdAt = $this->currentTime;
        }
        $entity->updatedAt = $this->currentTime;
        parent::persist($entity);
    }

    public function getRepository($className)
    {
        if (!isset($this->classCache[$className])) {
            /** @var AbstractEntityRepository $repo */
            $repo = parent::getRepository($className);

            // set dependencies (which could not be injected via construct)
            $repo->setEntityManager($this);
            $repo->setCurrentTime($this->currentTime);
            $repo->setLogger($this->logger);

            $this->classCache[$className] = $repo;
        }

        return $this->classCache[$className];
    }

    public function getImageRepo(): EntityRepository\ImageRepository
    {
        return $this->getRepository(Entity\Image::class);
    }

    public function getKeyValueRepo(): EntityRepository\KeyValueRepository
    {
        return $this->getRepository(Entity\KeyValue::class);
    }

    public function getNormalListingRepo(): EntityRepository\NormalListingRepository
    {
        return $this->getRepository(Entity\NormalListing::class);
    }

    public function getPageRepo(): EntityRepository\PageRepository
    {
        return $this->getRepository(Entity\Page::class);
    }

    public function getPageCategoryRepo(): EntityRepository\PageCategoryRepository
    {
        return $this->getRepository(Entity\PageCategory::class);
    }

    public function getPersonRepo(): EntityRepository\PersonRepository
    {
        return $this->getRepository(Entity\Person::class);
    }

    public function getProgrammeRepo(): EntityRepository\ProgrammeRepository
    {
        return $this->getRepository(Entity\Programme::class);
    }

    public function getSpecialListingRepo(): EntityRepository\SpecialListingRepository
    {
        return $this->getRepository(Entity\SpecialListing::class);
    }
}
