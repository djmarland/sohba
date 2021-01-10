<?php
declare(strict_types=1);

namespace App\Data\Database\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Data\Database\EntityRepository\PageRepository")
 * @ORM\Table(
 *     name="tblPages",
 *     indexes={
 *       @ORM\Index(name="page_uuid", columns={"id"}),
 *       @ORM\Index(name="page_url_path", columns={"url_path"})
 *     },
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
    public ?int $pkid = null;

    /**
     * @ORM\Column(type="string", name="title", length=50)
     */
    public string $title;

    /**
     * @ORM\Column(type="string", length=50, unique=true)
     */
    public string $urlPath;

    /**
     * @ORM\Column(type="text", name="html_content")
     */
    public string $htmlContent;

    /**
     * @ORM\Column(type="integer", name="menuOrder", length=6)
     */
    public int $order = 0;

    /**
     * @ORM\ManyToOne(targetEntity="PageCategory")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL", name="category", referencedColumnName="pkID")
     */
    public ?PageCategory $category = null;

    public function __construct(
        string $title,
        string $urlPath,
        string $htmlContent,
        int $order
    ) {
        parent::__construct();
        $this->title = $title;
        $this->order = $order;
        $this->urlPath = $urlPath;
        $this->htmlContent = $htmlContent;
    }
}
