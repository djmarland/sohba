<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use DateTimeImmutable;
use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\ImageRepository")
 * @ORM\Table(
 *     name="tblImages",
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
     * @ORM\Column(type="string", name="imgType", length=25)
     */
    public $type;

    /**
     * @ORM\Column(type="string", name="imgTitle", length=50)
     */
    public $title;

    /**
     * @ORM\Column(type="string", name="imgSize", length=25)
     */
    public $size;

    /**
     * @ORM\Column(type="blob", name="imgData")
     */
    public $data;

    public function __construct(
        UuidInterface $id,
        string $title,
        string $type
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->type = $type;
    }
}
