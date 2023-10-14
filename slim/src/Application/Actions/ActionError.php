<?php

declare(strict_types=1);

namespace App\Application\Actions;

use JsonSerializable;

class ActionError implements JsonSerializable
{
    final public const BAD_REQUEST = 'BAD_REQUEST';
    final public const INSUFFICIENT_PRIVILEGES = 'INSUFFICIENT_PRIVILEGES';
    final public const NOT_ALLOWED = 'NOT_ALLOWED';
    final public const NOT_IMPLEMENTED = 'NOT_IMPLEMENTED';
    final public const RESOURCE_NOT_FOUND = 'RESOURCE_NOT_FOUND';
    final public const SERVER_ERROR = 'SERVER_ERROR';
    final public const UNAUTHENTICATED = 'UNAUTHENTICATED';
    final public const VALIDATION_ERROR = 'VALIDATION_ERROR';
    final public const VERIFICATION_ERROR = 'VERIFICATION_ERROR';

    public function __construct(private string $type, private ?string $description)
    {
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;
        return $this;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(?string $description = null): self
    {
        $this->description = $description;
        return $this;
    }

    #[\ReturnTypeWillChange]
    public function jsonSerialize(): array
    {
        return [
            'type' => $this->type,
            'description' => $this->description,
        ];
    }
}
