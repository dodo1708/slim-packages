<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class MinIntPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'minint';
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
            return (int)$data;
        }
        return max((int)$data, $this->config['min']);
    }
}
