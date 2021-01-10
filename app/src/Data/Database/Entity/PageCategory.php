<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PageCategoryRepository")
 * @ORM\Table(
 *     name="tblPageCategories",
 *     indexes={
 *       @ORM\Index(name="page_category_uuid", columns={"id"})
 *     },
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
    public int $pkid;

    /**
     * @ORM\Column(type="string", name="name", length=50)
     */
    public string $title;

    /**
     * @ORM\Column(type="integer", name="catOrder", length=5)
     */
    public int $order;

    public function __construct(
        string $title,
        int $order
    ) {
        parent::__construct();
        $this->title = $title;
        $this->order = $order;
    }
}
