<?php

declare(strict_types=1);

namespace SlimAP\Handler;

use SlimAP\Document\AbstractProcessingDocument;
use SlimAP\ProcessingConfig\AbstractProcessingConfig;

class PreprocessingDataHandler
{
    public function handle(array $data, AbstractProcessingDocument $document): array
    {
        foreach ($document->getFields() as $field => $configOrDocument) {
            $data[$field] = $this->processField($data, $field, $configOrDocument);
        }

        return $data;
    }

    private function processField(array $data, string $field, AbstractProcessingDocument | AbstractProcessingConfig $configOrDocument): mixed
    {
        $value = $data[$field] ?? null;
        if ($value !== null) {
            if ($configOrDocument instanceof AbstractProcessingDocument) {
                if (is_array($value) && array_is_list($value)) {
                    // a list of documents
                    foreach ($value as $idx => $entry) {
                        foreach ($configOrDocument->getFields() as $f => $cOrD) {
                            $entry[$f] = $this->processField($entry, $f, $cOrD);
                        }
                        $value[$idx] = $entry;
                    }
                } else {
                    // a single document
                    foreach ($configOrDocument->getFields() as $f => $cOrD) {
                        $value[$f] = $this->processField($value, $f, $cOrD);
                    }
                }
            } else {
                if (is_array($value) && array_is_list($value)) {
                    // a list of values
                    foreach ($value as $idx => $entry) {
                        $value[$idx] = $configOrDocument->apply($entry);
                    }
                } else {
                    // a single value
                    $value = $configOrDocument->apply($value);
                }
            }
        }

        return $value;
    }
}
