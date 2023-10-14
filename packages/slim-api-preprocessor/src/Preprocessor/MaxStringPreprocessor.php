<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class MaxStringPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'maxstring';
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
            return (string)$data;
        }
        return mb_substr((string)$data, $this->config['max']);
    }
}
