<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class MaxFloatPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'maxfloat';
    }

    public static function getDefaultConfig(): array
    {
        return ['nullable' => false, 'max' => null];
    }

    public function process($data): mixed
    {
        if ($data === null && $this->config['nullable']) {
            return $data;
        }
        if ($this->config['max'] === null) {
            return (float)$data;
        }
        return min((float)$data, $this->config['max']);
    }
}
