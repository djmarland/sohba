<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PersonRepository")
 * @ORM\Table(
 *     name="tblPeople",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class Person extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="peoplePKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="string", name="peopleName", length=150)
     */
    public $name;

    /**
     * @ORM\Column(type="boolean", name="peopleExec", length=150)
     */
    public $isOnCommittee = false;

    /**
     * @ORM\Column(type="string", name="peopleTitle", length=100, nullable=true)
     */
    public $committeeTitle;

    /**
     * @ORM\Column(type="integer", name="peopleExecPosition", length=2, nullable=true)
     */
    public $committeeOrder;

    /**
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL", name="peopleImage", referencedColumnName="imgPKID")
     */
    public $image;

    public function __construct(
        UuidInterface $id,
        string $name
    ) {
        parent::__construct($id);
        $this->name = $name;
    }
}
