<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class UcStringPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'ucstring';
    }

    public static function getDefaultConfig(): array
    {
        return ['nullable' => false];
    }

    public function process($data): mixed
    {
        if ($data === null && $this->config['nullable']) {
            return $data;
        }
        return mb_strtoupper((string)$data);
    }
}
