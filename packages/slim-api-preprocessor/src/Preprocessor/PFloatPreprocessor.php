<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class PFloatPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'pfloat';
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
        return (float)$data;
    }
}
