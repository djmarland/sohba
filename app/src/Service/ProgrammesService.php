<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Programme as DbProgramme;
use App\Domain\Entity\Programme;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

class ProgrammesService extends AbstractService
{
    // exists only to provide a redirect from the old URLs to the UUID based ones
    public function findByLegacyId(int $id): ?Programme
    {
        return $this->mapSingle(
            $this->entityManager->getProgrammeRepo()->findByLegacyId($id),
            $this->programmeMapper
        );
    }

    public function findById(UuidInterface $id): ?Programme
    {
        return $this->mapSingle(
            $this->entityManager->getProgrammeRepo()->getByIdWithImage($id),
            $this->programmeMapper
        );
    }

    public function getAll(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findAll(),
            $this->programmeMapper
        );
    }

    public function getAllActive(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findAllActive(),
            $this->programmeMapper
        );
    }

    public function getAllRegular(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findByTypes([
                Programme::PROGRAMME_TYPE_REGULAR
            ]),
            $this->programmeMapper
        );
    }

    public function getAllEvents(): array
    {
        return $this->mapMany(
            $this->entityManager->getProgrammeRepo()->findByTypes(array_keys(
                Programme::PROGRAMME_EVENT_TYPES
            )),
            $this->programmeMapper
        );
    }

    public function getAllByPeople(?Programme $exclude = null): array
    {
        $results = $this->entityManager->getProgrammeRepo()->getProgrammesWithPeople();
        $groupedResults = [];

        foreach ($results as $programme) {
            foreach ($programme['people'] as $person) {
                $personId = $person['id'];

                if ($exclude && $exclude->getId()->equals($programme['id'])) {
                    continue;
                }

                if (!isset($groupedResults[(string)$personId])) {
                    $groupedResults[(string)$personId] = [];
                }
                $groupedResults[(string)$personId][] = $this->mapSingle($programme, $this->programmeMapper);
            }
        }

        return $groupedResults;
    }

    public function newProgramme(string $name, int $type = Programme::PROGRAMME_TYPE_REGULAR): UuidInterface
    {
        $page = new DbProgramme(
            $name,
            $type
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->id;
    }

    public function deleteProgramme(UuidInterface $programmeId): void
    {
        $this->entityManager->getProgrammeRepo()->deleteById($programmeId, DbProgramme::class);
    }


    public function updateProgramme(
        Programme $programme,
        string $name,
        string $tagLine,
        int $type,
        string $description,
        ?UuidInterface $imageId,
        array $peopleIds = []
    ): void {
        /** @var DbProgramme $entity */
        $entity = $this->entityManager->getProgrammeRepo()->getByID(
            $programme->getId(),
            Query::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new \InvalidArgumentException('Tried to update a programme that does not exist');
        }
        if (!Programme::isValidType($type)) {
            throw new \InvalidArgumentException('Invalid Type provided');
        }

        $entity->title = $name;
        $entity->tagline = $tagLine;
        $entity->type = $type;
        $entity->description = $description;
        $entity->image = $this->getAssociatedImageEntity($imageId);

        $entity->people = \array_map(function (UuidInterface $id) {
            $entity = $this->entityManager->getPersonRepo()->getByID(
                $id,
                Query::HYDRATE_OBJECT
            );
            if (!$entity) {
                throw new \InvalidArgumentException('Tried to update add a person that does not exist');
            }
            return $entity;
        }, $peopleIds);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
    }
}
