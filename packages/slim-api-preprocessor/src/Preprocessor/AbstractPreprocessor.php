<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

abstract class AbstractPreprocessor implements PreprocessorInterface
{
    protected array $config = [];

    public function __construct(?array $config = null)
    {
        $this->config = array_merge(static::getDefaultConfig(), $config ?: []);
    }

    public static function getInstance(array $config): PreprocessorInterface
    {
        return new static($config);
    }
}
