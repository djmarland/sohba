<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\ProgrammeRepository")
 * @ORM\Table(
 *     name="tblShows",
 *     indexes={
 *       @ORM\Index(name="shows_uuid", columns={"id"})
 *     },
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class Programme extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="showsPKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="string", name="showsTitle", length=200, nullable=true)
     */
    public $title;

    /**
     * @ORM\Column(type="string", name="showsTagline", length=400, nullable=true)
     */
    public $tagline;

    /**
     * @ORM\Column(type="text", name="showsDetail", nullable=true)
     */
    public $description;

    /**
     * @ORM\Column(type="integer", name="showsType", length=2)
     */
    public $type = 0;

    /**
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL", name="showsImage", referencedColumnName="imgPKID")
     */
    public $image;

    /**
     * @ORM\ManyToMany(targetEntity="Person")
     * @ORM\JoinTable(
     *     joinColumns={@ORM\JoinColumn(referencedColumnName="showsPKID", onDelete="CASCADE")},
     *     inverseJoinColumns={@ORM\JoinColumn(referencedColumnName="peoplePKID", onDelete="CASCADE")}
     * )
     */
    public $people;

    public function __construct(
        string $title,
        int $type
    ) {
        parent::__construct();
        $this->title = $title;
        $this->type = $type;
    }
}
