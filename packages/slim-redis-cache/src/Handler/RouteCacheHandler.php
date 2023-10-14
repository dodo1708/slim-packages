<?php

namespace SlimRC\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Routing\RouteContext;
use SlimRC\Attribute\CacheResponse;
use Symfony\Component\Cache\Adapter\RedisAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class RouteCacheHandler
{
    private Request $request;
    private RequestHandler $requestHandler;
    private string $callable;
    /** @var CacheResponse[] */
    private array $attributes = [];
    private ?Response $response = null;
    private readonly RedisAdapter $cache;

    public function __construct()
    {
        $redis = new \Redis();
        $redis->connect(getenv('REDIS_HOST'), getenv('REDIS_PORT') ?: 6379);
        $this->cache = new RedisAdapter($redis);
    }

    public function handle(Request $request, RequestHandler $requestHandler): Response
    {
        $this->init($request, $requestHandler);
        $this->loadCallable();
        $this->loadAttributes();
        if (empty($this->attributes)) {
            return $this->requestHandler->handle($request);
        }
        $this->checkCache();
        return $this->response;
    }

    private function init(Request $request, RequestHandler $requestHandler): void
    {
        $this->callable = '';
        $this->response = null;
        $this->attributes = [];
        $this->request = $request;
        $this->requestHandler = $requestHandler;
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
                $attributes = $method->getAttributes(CacheResponse::class);
                $this->attributes = array_map(static fn($a) => $a->newInstance(), $attributes);
            }
        }
    }

    private function checkCache(): void
    {
        if (empty($this->attributes)) {
            return;
        }
        $cacheKey = $this->getCacheKey();
        $responseContent = $this->cache->get($cacheKey, function (ItemInterface $item): string {
            $item->expiresAfter($this->getExpiration());
            return $this->getResponseContent();
        });
        $this->response = $this->getResponseFromContent($responseContent);
    }

    private function getCacheKey(): string
    {
        $attr = $this->attributes[0] ?? null;
        return $attr && $attr->key ? $attr->key : $this->getRequestCacheHash();
    }

    private function getExpiration(): ?int
    {
        $attr = $this->attributes[0] ?? null;
        return $attr && $attr->expiration ? $attr->expiration : null;
    }

    private function getContentType(): string
    {
        $attr = $this->attributes[0] ?? null;
        return $attr && $attr->contentType ? $attr->contentType : 'application/json';
    }

    private function pathOnly(): string
    {
        $attr = $this->attributes[0] ?? null;
        return $attr->pathOnly ?? false;
    }

    private function getRequestCacheHash(): string
    {
        $uri = $this->request->getUri();

        $keyComponents = $uri->getPath();
        if (!$this->pathOnly()) {
            $keyComponents .= $uri->getQuery();
        }

        return md5($keyComponents);
    }

    private function getResponseContent(): string
    {
        $response = $this->requestHandler->handle($this->request);
        return $response->getBody()->getContents();
    }

    private function getResponseFromContent(string $content): Response
    {
        $response = AppFactory::create()->getResponseFactory()->createResponse();
        $sf = new StreamFactory();
        $stream = $sf->createStream($content);
        return $response
            ->withBody($stream)
            ->withStatus(200)
            ->withHeader('Content-Type', $this->getContentType())
            ->withHeader('Content-Length', strlen($content));
    }
}
