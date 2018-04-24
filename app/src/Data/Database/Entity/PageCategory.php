<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PageCategoryRepository")
 * @ORM\Table(
 *     name="tblPageCategories",
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class PageCategory extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pkID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="string", name="name", length=50)
     */
    public $title;

    /**
     * @ORM\Column(type="integer", name="catOrder", length=5)
     */
    public $order;

    public function __construct(
        UuidInterface $id,
        string $title,
        int $order
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->order = $order;
    }
}
