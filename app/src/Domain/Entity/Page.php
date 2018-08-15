<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullPageCategory;
use App\Domain\Exception\DataNotFetchedException;
use App\Domain\ValueObject\RichText;
use Ramsey\Uuid\UuidInterface;

class Page extends Entity implements \JsonSerializable
{
    private $title;
    private $category;
    private $htmlContent;
    private $urlPath;
    private $navPosition;

    public function __construct(
        UuidInterface $id,
        string $title,
        RichText $htmlContent,
        ?string $urlPath = null,
        ?int $navPosition = null,
        ?PageCategory $category = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->category = $category;
        $this->htmlContent = $htmlContent;
        $this->urlPath = $urlPath;
        $this->navPosition = $navPosition;
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'urlPath' => $this->urlPath,
            'specialType' => null,
            'htmlContent' => $this->htmlContent->getContent(),
            'navPosition' => $this->navPosition,
        ];
        if ($this->category !== null) {
            $data['category'] = $this->getCategory();
        }
        return $data;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHtmlContent(): RichText
    {
        return $this->htmlContent;
    }

    public function getCategory(): ?PageCategory
    {
        if ($this->category === null) {
            throw new DataNotFetchedException(
                'Tried to use the page category, but it was not fetched'
            );
        }
        if ($this->category instanceof NullPageCategory) {
            return null;
        }
        return $this->category;
    }

    public function getUrlPath(): ?string
    {
        return $this->urlPath;
    }

    public function getNavPosition(): ?int
    {
        return $this->navPosition;
    }
}
