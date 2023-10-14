<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class MinFloatPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'minfloat';
    }

    public static function getDefaultConfig(): array
    {
        return ['nullable' => false, 'min' => null];
    }

    public function process($data): mixed
    {
        if ($data === null && $this->config['nullable']) {
            return $data;
        }
        if ($this->config['min'] === null) {
            return (float)$data;
        }
        return max((float)$data, $this->config['min']);
    }
}
