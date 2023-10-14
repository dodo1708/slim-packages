<?php

declare(strict_types=1);

namespace SlimAP\Handler;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use SlimAP\Document\AbstractProcessingDocument;
use SlimAP\Attribute\PreprocessBody;


class PreprocessingRequestHandler
{

    private Request $request;
    private string $callable;
    private array $attributes = [];
    private array $documents = [];

    public function __construct()
    {
    }

    public function handle(Request $request): Request
    {
        $this->init($request);
        $this->loadCallable();
        $this->loadAttributes();
        $this->initDocuments();
        $this->preprocessRequestPayload();
        return $this->request;
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
                $attributes = $method->getAttributes(PreprocessBody::class);
                $this->attributes = array_map(static fn ($a) => $a->newInstance(), $attributes);
            }
        }
    }

    private function initDocuments(): void
    {
        foreach ($this->attributes as $attribute) {
            /** @var AbstractProcessingDocument $doc */
            $doc = new $attribute->documentClass();
            $doc->init();
            $this->documents[] = [
                'attribute' => $attribute,
                'document' => $doc,
            ];
        }
    }

    private function preprocessRequestPayload(): void
    {
        $bodyContent = $this->request->getBody()->getContents();
        $jsonData = json_decode($bodyContent, true);
        foreach ($this->documents as $documentInfo) {
            $attribute = $documentInfo['attribute'];
            /** @var SchemaValidator $validator */
            $document = $documentInfo['document'];

            $h = new PreprocessingDataHandler();

            if ($attribute->key && isset($jsonData[$attribute->key])) {
                $jsonData[$attribute->key] = $h->handle($jsonData[$attribute->key], $document);
            } else {
                $jsonData = $h->handle($jsonData, $document);
            }
        }
        $this->request = $this->request->withParsedBody($jsonData);
    }
}
