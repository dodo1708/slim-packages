<?php

declare(strict_types=1);

namespace SlimAP\Document;

use SlimAP\ProcessingConfig\AbstractProcessingConfig;

abstract class AbstractProcessingDocument
{
    private array $document = [];

    protected function addField(string $field, AbstractProcessingConfig | AbstractProcessingDocument $configOrDocument): void
    {
        $this->document[$field] = $configOrDocument;
    }

    protected function addFields(array $fields): void
    {
        foreach ($fields as $field => $configOrDocument) {
            assert($configOrDocument instanceof AbstractProcessingConfig || $configOrDocument instanceof AbstractProcessingDocument);
            $this->addField($field, $configOrDocument);
        }
    }

    public function getFields(): array
    {
        return $this->document;
    }

    abstract public function init(): void;
}
