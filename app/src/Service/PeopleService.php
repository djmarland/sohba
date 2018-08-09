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
        $page = new DbPerson($name);

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
        Person $person,
        string $name,
        bool $onExec,
        ?string $committeeTitle,
        ?int $committeePosition,
        $imageId = null // todo - add typehint for UUID
    ): void {
        $entity = $this->entityManager->getPersonRepo()->getByID(
            $person->getId(),
            Query::HYDRATE_OBJECT
        );
        if (!$entity) {
            throw new \InvalidArgumentException('Tried to update a person that does not exist');
        }

        $entity->name = $name;
        $entity->isOnCommittee = $onExec;
        $entity->committeeTitle = $committeeTitle;
        $entity->committeeOrder = $committeePosition;
        $entity->image = $this->getAssociatedImageEntity($imageId);

        $this->entityManager->persist($entity);
        $this->entityManager->flush();
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

            $personInShow = new DbPersonInShow();
            $personInShow->person = $personEntity;
            $personInShow->programme = $programmeEntity;

            $this->entityManager->persist($personInShow);
        }
        $this->entityManager->flush();
    }

    public function migrate(): void
    {
        $qb = $this->entityManager->getPersonRepo()->createQueryBuilder('tbl')
            ->select('tbl')
            ->where('tbl.uuid = :nothing')
            ->setParameter('nothing', '');

        $results = $qb->getQuery()->getResult();
        foreach ($results as $result) {
            /** @var \App\Data\Database\Entity\Person $result */
            $newId = ID::makeNewID(\App\Data\Database\Entity\Person::class);
            $result->id = $newId;
            $result->uuid = (string)$newId;
            $result->createdAt = new \DateTimeImmutable('2017-01-01T00:00:00Z');
            $result->updatedAt = new \DateTimeImmutable('2017-01-01T00:00:00Z');
            $this->entityManager->persist($result);
        }
        $this->entityManager->flush();
    }
}
