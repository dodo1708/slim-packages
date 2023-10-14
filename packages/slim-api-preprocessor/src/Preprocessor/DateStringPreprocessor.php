<?php

declare(strict_types=1);

namespace SlimAP\Preprocessor;

class DateStringPreprocessor extends AbstractPreprocessor
{
    public static function getIdentifier(): string
    {
        return 'datestring';
    }

    public static function getDefaultConfig(): array
    {
        return ['nullable' => true, 'fromFormat' => ['Y-m-d'], 'format' => 'Y-m-d'];
    }

    public function process($data): mixed
    {
        if ($data === null && $this->config['nullable']) {
            return $data;
        }
        foreach ($this->config['fromFormat'] as $dateFormat) {
            if ($date = \DateTime::createFromFormat($dateFormat, (string)$data)) {
                return $date->format($this->config['format']);
            }
        }
        return null;
    }
}
