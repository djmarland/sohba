<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\ImageRepository")
 * @ORM\Table(
 *     name="tblImages",
 *     indexes={@ORM\Index(name="image_title", columns={"imgTitle"})},
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class Image extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="imgPKID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="string", name="imgType", length=25, nullable=true)
     */
    public $type;

    /**
     * @ORM\Column(type="string", name="imgTitle", length=50)
     */
    public $title;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    public $fileName;

    /**
     * @ORM\Column(type="string", name="imgSize", length=25, nullable=true)
     */
    public $size;

    /**
     * @ORM\Column(type="blob", name="imgData", nullable=true)
     */
    public $data;

    public function __construct(
        UuidInterface $id,
        string $title
    ) {
        parent::__construct($id);
        $this->title = $title;
    }
}
