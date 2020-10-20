<?php
declare(strict_types=1);

namespace App\Domain\Entity;

use App\Domain\ValueObject\RichText;
use Ramsey\Uuid\UuidInterface;

class ConfigurableContent extends Entity implements \JsonSerializable
{
    private $key;
    private $description;
    private $simpleContent;
    private $richContent;

    public function __construct(
        UuidInterface $id,
        string $key,
        string $description,
        ?string $simpleContent = null,
        ?RichText $richContent = null
    ) {
        parent::__construct($id);
        $this->key = $key;
        $this->description = $description;
        $this->simpleContent = $simpleContent;
        $this->richContent = $richContent;
    }

    public function __toString(): string
    {
        if ($this->richContent) {
            return $this->richContent->getContent();
        }
        return $this->simpleContent ?? '';
    }

    public function jsonSerialize()
    {
        return [
            'richContent' => $this->richContent,
            'simpleContent' => $this->simpleContent,
            'description' => $this->description,
            'isRichText' => $this->isRichText(),
        ];
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function hasValue(): bool
    {
        return $this->richContent !== null || $this->simpleContent !== null;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getSimpleContent(): ?string
    {
        return $this->simpleContent;
    }

    public function getRichContent(): ?RichText
    {
        return $this->richContent;
    }

    public function isRichText(): bool
    {
        return $this->richContent !== null;
    }
}
