<?php

namespace SlimAV\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Exception\HttpBadRequestException;
use Slim\Routing\RouteContext;
use SlimAV\Config\SchemaConfig;
use SlimAV\Exception\SlimAVException;
use SlimAV\Validator\SchemaValidator;

class RouteValidationHandler
{
    private Request $request;
    private string $callable;
    private array $attributes = [];
    private array $schemaValidators = [];

    public function __construct()
    {
    }

    public function handle(Request $request)
    {
        $this->init($request);
        $this->loadCallable();
        $this->loadAttributes();
        $this->initValidators();
        $this->validateRequestPayload();
    }

    private function init(Request $request): void
    {
        $this->callable = '';
        $this->attributes = [];
        $this->request = $request;
    }

    private function loadCallable(): void
    {
        $routeContext = RouteContext::fromRequest($this->request);
        $route = $routeContext->getRoute();
        // callable should be something like MyApp\Controller\MyController:function
        $this->callable = $route->getCallable();
    }

    private function loadAttributes(): void
    {
        if (count($parts = explode(':', $this->callable)) === 2) {
            $class = $parts[0];
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $method = $reflection->getMethod($parts[1]);
                $this->attributes = $method->getAttributes(SetUp::class);
            }
        }
    }

    private function initValidators(): void
    {
        foreach ($this->attributes as $attribute) {
            $schemaPath = $attribute->schemaPath;
            if (!str_starts_with((string) $schemaPath, '/')) {
                $schemaPath = SchemaConfig::getInstance()->getSchemaRootPath() . $schemaPath;
            }
            if (!file_exists($schemaPath)) {
                throw new SlimAVException(sprintf('The given schema file does not exist at the location "%s"!', $schemaPath));
            }

            $this->schemaValidators[] = [
                'attribute' => $attribute,
                'validator' => new SchemaValidator(
                    sprintf('file://%s', md5((string) $schemaPath)),
                    $schemaPath
                ),
            ];
        }
    }

    private function validateRequestPayload(): void
    {
        $bodyContent = $this->request->getBody()->getContents();
        $jsonData = json_decode($bodyContent, true);
        foreach ($this->schemaValidators as $schemaValidator) {
            $attribute = $schemaValidator['attribute'];
            /** @var SchemaValidator $validator */
            $validator = $schemaValidator['validator'];
            if ($attribute->key && isset($jsonData[$attribute->key])) {
                $errors = $validator->validate($jsonData[$attribute->key]);
            } else {
                $errors = $validator->validate($jsonData);
            }
            if (!empty($errors)) {
                $errMsg = $this->buildValidationErrorMessage($attribute, $errors);
                throw new HttpBadRequestException($this->request, $errMsg);
            }
        }
    }

    private function buildValidationErrorMessage(\ReflectionAttribute $attribute, array $errors): string
    {
        if ($processor = SchemaConfig::getInstance()->getErrorMessageProcessor()) {
            return $processor($this->request, $attribute, $errors);
        }
        return json_encode([
            'message' => sprintf('Error validating request payload with schema "%s"', basename((string) $attribute->schemaPath)),
            'errors' => $errors,
            'key' => $attribute->key ?: null,
        ]);
    }
}
