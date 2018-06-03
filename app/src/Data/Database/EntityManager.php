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
    private $currentTime;
    private $logger;

    private $classCache = [];

    public function __construct(
        EntityManagerInterface $entityManager,
        DateTimeImmutable $currentTime,
        LoggerInterface $logger
    ) {
        parent::__construct($entityManager);
        $this->currentTime = $currentTime;
        $this->logger = $logger;
    }

    public function persist($entity)
    {
        /** @var AbstractEntity $entity */

        // interject to update the created_at/updated_at fields (for audit purposes)
        $entity->updatedAt = $this->currentTime;
        if (!$entity->createdAt) {
            $entity->createdAt = $this->currentTime;
        }
        parent::persist($entity);
    }

    public function getRepository($entityName)
    {
        if (!isset($this->classCache[$entityName])) {
            /** @var AbstractEntityRepository $repo */
            $repo = parent::getRepository($entityName);

            // set dependencies (which could not be injected via construct)
            $repo->setEntityManager($this);
            $repo->setCurrentTime($this->currentTime);
            $repo->setLogger($this->logger);

            $this->classCache[$entityName] = $repo;
        }

        return $this->classCache[$entityName];
    }

    public function getAll()
    {
        $entityFiles = \scandir(__DIR__ . '/Entity/');
        $results = \array_map(
            function ($className) {
                $fullEntityName = __NAMESPACE__ . '\\Entity\\' . \str_replace('.php', '', $className);
                if (\class_exists($fullEntityName) && \is_subclass_of($fullEntityName, AbstractEntity::class)) {
                    return $this->getRepository($fullEntityName);
                }

                return null;
            },
            $entityFiles
        );

        return \array_filter($results);
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

    public function getPersonInShowRepo(): EntityRepository\PersonInShowRepository
    {
        return $this->getRepository(Entity\PersonInShow::class);
    }

    public function getProgrammeRepo(): EntityRepository\ProgrammeRepository
    {
        return $this->getRepository(Entity\Programme::class);
    }

    public function getSpecialDayRepo(): EntityRepository\SpecialDayRepository
    {
        return $this->getRepository(Entity\SpecialDay::class);
    }

    public function getSpecialListingRepo(): EntityRepository\SpecialListingRepository
    {
        return $this->getRepository(Entity\SpecialListing::class);
    }
}
