<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class StepIntPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'stepint';
    }

    public static function getDefaultConfig(): array
    {
        return ['nullable' => false, 'step' => 1];
    }

    public function process($data): mixed
    {
        if ($data === null && $this->config['nullable']) {
            return $data;
        }
        return (int)((int)$data / $this->config['step']) * $this->config['step'];
    }
}
