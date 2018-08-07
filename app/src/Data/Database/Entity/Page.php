<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ramsey\Uuid\UuidInterface;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PageRepository")
 * @ORM\Table(
 *     name="tblPages",
 *     indexes={@ORM\Index(name="page_url_path", columns={"url_path"})},
 *     options={"collate"="utf8mb4_unicode_ci", "charset"="utf8mb4"},
 * )
 */
class Page extends AbstractEntity
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer", name="pkID")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    public $pkid;

    /**
     * @ORM\Column(type="string", name="title", length=50)
     */
    public $title;

    /**
     * @ORM\Column(type="string", length=50, nullable=false, unique=true)
     */
    public $urlPath;

    /**
     * @ORM\Column(type="text", name="content")
     */
    public $content;

    /**
     * @ORM\Column(type="text", name="html_content", nullable=true)
     */
    public $htmlContent;

    /**
     * @ORM\Column(type="integer", name="menuOrder", length=6)
     */
    public $order = 0;

    /**
     * @ORM\ManyToOne(targetEntity="PageCategory")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL", name="category", referencedColumnName="pkID")
     */
    public $category = null;

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
