<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Person as DbPerson;
use App\Data\Database\Entity\PersonInShow as DbPersonInShow;
use App\Data\ID;
use App\Domain\Entity\Person;
use App\Domain\Entity\Programme;
use Doctrine\ORM\Query;

class PeopleService extends AbstractService
{
    public function findAll(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAll(),
            $this->personMapper
        );
    }

    public function findExecutiveCommittee(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAllByCommittee(true),
            $this->personMapper
        );
    }

    public function findOtherMembers(): array
    {
        return $this->mapMany(
            $this->entityManager->getPersonRepo()->findAllByCommittee(false),
            $this->personMapper
        );
    }

    public function findForProgramme(Programme $programme): array
    {
        $peopleInProgramme = $this->entityManager->getPersonInShowRepo()
            ->findPeopleForProgrammeId($programme->getLegacyId());

        $people = array_map(function (array $personInProgramme) {
            return $personInProgramme['person'];
        }, $peopleInProgramme);

        return $this->mapMany(
            $people,
            $this->personMapper
        );
    }

    public function newPerson(string $name): int
    {
        $page = new DbPerson(
            ID::makeNewID(DbPerson::class),
            $name
        );

        $this->entityManager->persist($page);
        $this->entityManager->flush();

        return $page->pkid;
    }

    public function deletePerson(int $programmeId): void
    {
        $this->entityManager->getPersonRepo()->deleteByLegacyId($programmeId);
    }

    public function findByLegacyId($legacyId): ?Person
    {
        return $this->mapSingle(
            $this->entityManager->getPersonRepo()->findByLegacyId($legacyId),
            $this->personMapper
        );
    }

    public function updatePerson(
        Person $page,
        string $name,
        bool $onExec,
        ?string $committeeTitle,
        ?int $committeePosition,
        ?int $imageId
    ): void {
        $this->entityManager->getPersonRepo()->updatePerson(
            $page->getLegacyId(),
            $name,
            $onExec,
            $committeeTitle,
            $committeePosition,
            $imageId
        );
    }

    public function setPeopleForProgramme(array $peopleIds, Programme $programme): void
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

            $personInShow = new DbPersonInShow(
                ID::makeNewID(DbPersonInShow::class)
            );
            $personInShow->person =$personEntity;
            $personInShow->programme = $programmeEntity;

            $this->entityManager->persist($personInShow);
        }
        $this->entityManager->flush();
    }
}
