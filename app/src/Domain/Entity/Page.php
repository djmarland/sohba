<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\Entity\Null\NullPageCategory;
use App\Domain\Exception\DataNotFetchedException;
use Ramsey\Uuid\UuidInterface;

class Page extends Entity implements \JsonSerializable
{
    private $title;
    private $category;
    private $legacyId;
    private $htmlContent;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $title,
        string $htmlContent,
        ?PageCategory $category = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->category = $category;
        $this->legacyId = $legacyId;
        $this->htmlContent = $htmlContent;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'title' => $this->getTitle(),
        ];
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHtmlContent(): string
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
}
