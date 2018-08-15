<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\PersonInShow;
use App\Data\Database\Entity\Programme as DbProgramme;
use App\Domain\Entity\Person;
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

    public function getAllByPeople(array $people = [], ?Programme $exclude = null): array
    {
        $peopleEntities = array_map(function (Person $person) {
            return $this->entityManager->getPersonRepo()->getByID(
                $person->getId(),
                Query::HYDRATE_OBJECT
            );
        }, $people);

        // todo - use the ManyToMany map
        $results = $this->entityManager->getPersonInShowRepo()->findAll($peopleEntities);
        $groupedResults = [];

        foreach ($results as $result) {
            $personId = $result['person']['id'];

            if ($exclude && $exclude->getId()->equals($result['programme']['id'])) {
                continue;
            }

            if (!isset($groupedResults[(string)$personId])) {
                $groupedResults[(string)$personId] = [];
            }
            $groupedResults[(string)$personId][] = $this->mapSingle($result['programme'], $this->programmeMapper);
        }

        return $groupedResults;
    }

    public function newProgramme(string $name, int $type = Programme::PROGRAMME_TYPE_REGULAR): int
    {
        $page = new DbProgramme(
            $name,
            $type
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
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

        // todo - remove this
        $this->setPeopleForProgramme($peopleIds, $programme);

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

    /**
     * @deprecated todo - remove after replaced with manyToMany
     */
    private function setPeopleForProgramme(array $peopleIds, Programme $programme): void
    {
        $programmeEntity = $this->entityManager->getProgrammeRepo()->getByID(
            $programme->getId(),
            Query::HYDRATE_OBJECT
        );
        $this->entityManager->getPersonInShowRepo()->deleteAllForProgramme($programmeEntity);

        foreach ($peopleIds as $personId) {
            /** @var UuidInterface $personId */
            $personEntity = $this->entityManager->getPersonRepo()->getByID(
                $personId,
                Query::HYDRATE_OBJECT
            );

            $personInShow = new PersonInShow();
            $personInShow->person = $personEntity;
            $personInShow->programme = $programmeEntity;

            $this->entityManager->persist($personInShow);
        }
        $this->entityManager->flush();
    }

    public function migratePeopleInShows(): void
    {
        $all = $this->entityManager->getPersonInShowRepo()->findAll([], Query::HYDRATE_OBJECT);
        $progs = [];
        foreach ($all as $e) {
            /** @var PersonInShow $e */
            $progId = $e->programme->pkid;
            if (!isset($progs[$progId])) {
                $progs[$progId] = $e->programme;
                $progs[$progId]->people = [];
            }
            $progs[$progId]->people[] = $e->person;
        }

        foreach ($progs as $prog) {
            $this->entityManager->persist($prog);
        }
        $this->entityManager->flush();
    }
}
