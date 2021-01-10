<?php
declare(strict_types=1);

namespace App\Presenter\Message;

use JsonSerializable;

abstract class AbstractMessagePresenter implements JsonSerializable
{
    protected const TYPE = 'info';
    private string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function getType(): string
    {
        return static::TYPE;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function jsonSerialize(): array
    {
        return [
            'type' => static::TYPE,
            'message' => $this->message,
        ];
    }
}
