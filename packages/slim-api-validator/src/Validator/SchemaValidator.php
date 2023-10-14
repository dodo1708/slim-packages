<?php

declare(strict_types=1);

namespace SlimAV\Validator;

use JsonSchema\Constraints\Factory;
use JsonSchema\SchemaStorage;
use JsonSchema\Validator;

class SchemaValidator
{
    protected Validator $validator;
    protected string $jsonSchemaObject;

    public function __construct(string $schemaId, string $schemePath)
    {
        $this->jsonSchemaObject = file_get_contents(__DIR__ . $schemePath);
        $schemaStorage = new SchemaStorage();
        $schemaStorage->addSchema($schemaId, json_decode($this->jsonSchemaObject));
        $this->validator = new Validator(new Factory($schemaStorage));
    }

    public function validateJSON(mixed $contentToValidate): true | array
    {
        $this->validator->validate($contentToValidate, json_decode($this->jsonSchemaObject));

        if ($this->validator->isValid()) {
            return true;
        }

        return $this->validator->getErrors();
    }

    public function validate(array $data): ?array
    {
        $validationResult = $this->validateJSON(json_decode(json_encode($data), false));

        if ($validationResult !== true) {
            $errors = [];
            foreach ($validationResult as $error) {
                $errors[$error['property']] = $error['message'];
            }
            return $errors;
        }

        return null;
    }
}
