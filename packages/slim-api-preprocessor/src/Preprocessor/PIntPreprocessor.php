<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class PIntPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'pint';
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
        return abs((int)$data);
    }
}
