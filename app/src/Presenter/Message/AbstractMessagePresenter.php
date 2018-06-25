<?php
declare(strict_types=1);

namespace App\Presenter\Message;

abstract class AbstractMessagePresenter implements \JsonSerializable
{
    private $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function jsonSerialize()
    {
        return [
            'type' => static::TYPE,
            'message' => $this->message
        ];
    }
}
