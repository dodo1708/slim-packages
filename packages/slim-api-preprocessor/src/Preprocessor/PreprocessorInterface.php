<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

interface PreprocessorInterface
{
    public function __construct(array $config);

    public static function getInstance(array $config): PreprocessorInterface;

    public static function getIdentifier(): string;

    public static function getDefaultConfig(): array;

    public function process($data): mixed;
}
