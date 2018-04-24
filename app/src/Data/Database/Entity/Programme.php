<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\ProgrammeRepository")
 * @ORM\Table(
 *     name="tblShows",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class Programme extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="showsPKID")
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
     * @ORM\Column(type="string", name="showsDetail", length=5000, nullable=true)
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

    public function __construct(
        UuidInterface $id,
        string $title
    ) {
        parent::__construct($id);
        $this->title = $title;
    }
}
