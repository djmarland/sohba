<?php
declare(strict_types=1);

namespace App\Service;

use App\Data\Database\Entity\Person as DbPerson;
use App\Domain\Entity\Person;
use App\Domain\Entity\Programme;
use Doctrine\ORM\Query;
use Ramsey\Uuid\UuidInterface;

class PeopleService extends AbstractService
{
    public function findById(UuidInterface $id): ?Person
    {
        return $this->mapSingle(
            $this->entityManager->getPersonRepo()->getByIdWithImage($id),
            $this->personMapper
        );
    }

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
        // todo - use the new ManyToMany map
        $entity = $this->entityManager->getProgrammeRepo()->getByID(
            $programme->getId(),
            Query::HYDRATE_OBJECT
        );
        $peopleInProgramme = $this->entityManager->getPersonInShowRepo()
            ->findPeopleForProgramme($entity);

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

    public function deletePerson(UuidInterface $programmeId): void
    {
        $this->entityManager->getPersonRepo()->deleteById($programmeId, DbPerson::class);
    }

    public function updatePerson(
        Person $person,
        string $name,
        bool $onExec,
        ?string $committeeTitle,
        ?int $committeePosition,
        ?UuidInterface $imageId
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
}
