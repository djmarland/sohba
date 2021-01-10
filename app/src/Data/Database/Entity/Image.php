<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\ImageRepository")
 * @ORM\Table(
 *     name="tblImages",
 *     indexes={
 *       @ORM\Index(name="image_uuid", columns={"id"}),
 *       @ORM\Index(name="image_title", columns={"imgTitle"})
 *     },
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
    public int $pkid;

    /**
     * @ORM\Column(type="string", name="imgTitle", length=50)
     */
    public string $title;

    /**
     * @ORM\Column(type="string", length=100)
     */
    public string $fileName;

    public function __construct(
        string $title
    ) {
        parent::__construct();
        $this->title = $title;
    }
}
