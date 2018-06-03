<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PersonInShowRepository")
 * @ORM\Table(
 *     name="xtblShowPeople",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class PersonInShow extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="spPKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spShow", referencedColumnName="showsPKID")
     */
    public $programme;

    /**
     * @ORM\ManyToOne(targetEntity="Person")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE", name="spPerson", referencedColumnName="peoplePKID")
     */
    public $person;

    public function __construct(
        UuidInterface $id
    ) {
        parent::__construct($id);
    }
}
