<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class BoolPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'bool';
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

        $options = 0;
        if ($this->config['nullable']) {
            $options = FILTER_NULL_ON_FAILURE;
        }
        return filter_var($data, FILTER_VALIDATE_BOOLEAN, $options);
    }
}
