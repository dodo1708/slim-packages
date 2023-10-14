<?php

declare(strict_types=1);

namespace SlimAV\Config;

use SlimAV\Exception\SlimAVException;

final class SchemaConfig
{
    private SchemaConfig $instance = null;

    private string $rootPath = '/schemas';
    private ?mixed $extraErrorPayload = null;
    private ?callable $errorMessageProcessor = null;

    private function __construct()
    {
    }

    public static function getInstance(): SchemaConfig
    {
        if (self::$instance === null) {
            self::$instance = new SchemaConfig();
        }
        return self::$instance;
    }

    public function getSchemaRootPath(): string
    {
        return $this->rootPath;
    }

    public function setSchemaRootPath(string $rootPath): SchemaConfig
    {
        $this->rootPath = $rootPath;

        return $this;
    }

    /**
     * Can be either a fixxed payload in form of an array or a callable.
     * The callable must accept three arguments: The request, the attribute and the validation error.
     */
    public function setExtraErrorPayload(?mixed $extraErrorPayload): static
    {
        if (!is_callable($extraErrorPayload) && !is_array($extraErrorPayload)) {
            throw new SlimAVException(sprintf('Parameter $extraErrorPayload must be either callable or array.'));
        }
        $this->extraErrorPayload = $extraErrorPayload;

        return $this;
    }

    public function getExtraErrorPayload(): ?mixed
    {
        return $this->extraErrorPayload;
    }

    /**
     * The callable must accept three arguments: The request, the attribute and the validation error.
     */
    public function setErrorMessageProcessor(?callable $errorMessageProcessor): static
    {
        $this->errorMessageProcessor = $errorMessageProcessor;

        return $this;
    }

    public function getErrorMessageProcessor(): ?callable
    {
        return $this->errorMessageProcessor;
    }
}
