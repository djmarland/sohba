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
    private $legacyId;
    private $htmlContent;
    private $legacyContent;
    private $urlPath;
    private $navPosition;

    public function __construct(
        UuidInterface $id,
        int $legacyId,
        string $title,
        string $legacyContent,
        ?RichText $htmlContent,
        ?string $urlPath = null,
        ?int $navPosition = null,
        ?PageCategory $category = null
    ) {
        parent::__construct($id);
        $this->title = $title;
        $this->category = $category;
        $this->legacyId = $legacyId;
        $this->legacyContent = $legacyContent;
        $this->htmlContent = $htmlContent;
        $this->urlPath = $urlPath;
        $this->navPosition = $navPosition;
    }

    public function jsonSerialize()
    {
        $data = [
            'id' => $this->id,
            'title' => $this->getTitle(),
            'legacyId' => $this->legacyId,
            'urlPath' => $this->urlPath,
            'specialType' => null,
            'legacyContent' => $this->legacyContent,
            'htmlContent' => $this->htmlContent ? $this->htmlContent->getContent() : null,
            'navPosition' => $this->navPosition,
        ];
        if ($this->category !== null) {
            $data['category'] = $this->getCategory();
        }
        return $data;
    }

    public function getLegacyId(): int
    {
        return $this->legacyId;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getHtmlContent() // : RichText
    {
        return $this->htmlContent ?? $this->parseLegacyContent();
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

    private function parseLegacyContent(): string
    {
        // parse html the legacy way
        // CONVERT LINE BREAKS
        $pageContent = \nl2br($this->legacyContent);

        // CONVERT FORMATTING

        //bold
        $pageContent = \str_replace('[b]', '<strong>', $pageContent);
        $pageContent = \str_replace('[/b]', '</strong>', $pageContent);
        //italic
        $pageContent = \str_replace('[i]', '<em>', $pageContent);
        $pageContent = \str_replace('[/i]', '</em>', $pageContent);
        //link
        $pageContent = \str_replace('" Ltitle="', '">', $pageContent);
        $pageContent = \str_replace('[link-address="', '<a href="', $pageContent);
        $pageContent = \str_replace('"-link]', '</a>', $pageContent);
        //mailLink
        $pageContent = \str_replace('[mail-address="', '<a href="mailto:', $pageContent);
        $pageContent = \str_replace('"-mail]', '</a>', $pageContent);

        // CONVERT IMAGES
        //left
        $pageContent = \str_replace('[IMGL]', '<img class="image--left" src="/image.php?i=', $pageContent);
        $pageContent = \str_replace('[/IMGL]', '" />', $pageContent);
        //right
        $pageContent = \str_replace('[IMGR]', '<img class="image--right" src="/image.php?i=', $pageContent);
        $pageContent = \str_replace('[/IMGR]', '" />', $pageContent);
        //center
        $pageContent = \str_replace(
            '[IMGC]',
            '</p><p class="text--center"><img class="image--center" src="/image.php?i=',
            $pageContent
        );
        $pageContent = \str_replace('[/IMGC]', '" /></p><p>', $pageContent);

        // FINALLY CONVERT SAFE TAGS INC CLOSING TO HTML AND CONVERT CLOSING SQUARE BRACKETS
        $pageContent = \str_replace('[div', '</p><div', $pageContent);
        $pageContent = \str_replace('[/div]', '</div><p>', $pageContent);
        $pageContent = \str_replace('[span', '<span', $pageContent);
        $pageContent = \str_replace('[/span', '</span', $pageContent);
        //headings and horizontal rule
        $pageContent = \str_replace('[h', '<h', $pageContent);
        $pageContent = \str_replace('[/h', '</h', $pageContent);
        //tables
        $pageContent = \str_replace('[t', '<t', $pageContent);
        $pageContent = \str_replace('[/t', '</t', $pageContent);
        $pageContent = \str_replace('[u', '<u', $pageContent);
        $pageContent = \str_replace('[/u', '</u', $pageContent);
        $pageContent = \str_replace('[o', '<o', $pageContent);
        $pageContent = \str_replace('[/o', '</o', $pageContent);
        $pageContent = \str_replace('[l', '<l', $pageContent);
        $pageContent = \str_replace('[/l', '</l', $pageContent);

        $pageContent = \str_replace(']', '>', $pageContent);

        $pageContent = \str_replace('<br /><br />', '<br />', $pageContent);

        return $pageContent;
    }
}
