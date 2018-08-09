<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\PersonInShow;
use App\Data\Database\Entity\Programme as DbProgramme;
use App\Domain\Entity\Programme;
use Doctrine\ORM\Query;

class ProgrammesService extends AbstractService
{
    public function findByLegacyId(int $id): ?Programme
    {
        return $this->mapSingle(
            $this->entityManager->getProgrammeRepo()->findByLegacyId($id),
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

    public function getAllByPersonIds(array $ids = [], ?Programme $exclude = null): array
    {
        $results = $this->entityManager->getPersonInShowRepo()->findAll($ids);
        $groupedResults = [];

        foreach ($results as $result) {
            if ($exclude && $exclude->getLegacyId() === $result['programme']['pkid']) {
                continue;
            }

            $personId = $result['person']['pkid'];
            if (!isset($groupedResults[$personId])) {
                $groupedResults[$personId] = [];
            }
            $groupedResults[$personId][] = $this->mapSingle($result['programme'], $this->programmeMapper);
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

    public function deleteProgramme(int $programmeId): void
    {
        $this->entityManager->getProgrammeRepo()->deleteByLegacyId($programmeId);
    }

    public function updateProgramme(
        Programme $programme,
        string $name,
        string $tagLine,
        int $type,
        string $description,
        $imageId = null, // todo - add typehint for UUID,
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

        $entity->people = \array_map(function (int $id) {
            $entity = $this->entityManager->getPersonRepo()->findByLegacyId(
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
        $programmeId = $programme->getLegacyId();

        $this->entityManager->getPersonInShowRepo()->deleteAllForProgrammeId($programmeId);

        $programmeEntity = $this->entityManager->getProgrammeRepo()->findByLegacyId(
            $programmeId,
            Query::HYDRATE_OBJECT
        );
        foreach ($peopleIds as $personId) {
            $personEntity = $this->entityManager->getPersonRepo()->findByLegacyId(
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
