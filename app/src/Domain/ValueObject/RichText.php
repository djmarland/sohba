<?php
declare(strict_types=1);

namespace App\Domain\ValueObject;

class RichText implements \JsonSerializable
{
    private const /** @noinspection RequiredAttributes */
        ALLOWED_TAGS = '<p><br><ul><ol><li><a><img>' .
        '<h2><h3><h4><h5><h6><strong><del><em><blockquote>';

    private $safeContent;

    public function __construct(string $inputContent)
    {
        $this->safeContent = \strip_tags($inputContent, self::ALLOWED_TAGS);
    }

    public function getContent(): string
    {
        return $this->safeContent;
    }

    public function getContentForDisplay(): string
    {
        $content = $this->safeContent;

        // collapse to one line
        $content = str_replace(["\n", "\r"], '', $content);

        // convert </p> followed by populated <p> to a single <br />
        $content = preg_replace('/<\/p><p>(?!<\/p>)/i', '<br>$1', $content);

        // remove empty paragraphs
        $content = str_replace(['<p></p>', '<p><br>'], ['', '<p>'], $content);

        return $content;
    }

    public function __toString()
    {
        return $this->getContentForDisplay();
    }

    public function jsonSerialize()
    {
        return $this->__toString();
    }
}
