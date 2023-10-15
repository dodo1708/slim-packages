<?php

namespace SlimRC\Handler;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Factory\AppFactory;
use Slim\Psr7\Factory\StreamFactory;
use Slim\Routing\RouteContext;
use SlimRC\Attribute\CacheResponse;
use SlimRC\Control\CacheControl;
use SlimRC\Service\RedisConnectionService;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;
use Symfony\Contracts\Cache\ItemInterface;

class RouteCacheHandler
{
    private Request $request;
    private RequestHandler $requestHandler;
    private string $callable;
    /** @var CacheResponse[] */
    private array $attributes = [];
    private ?Response $response = null;
    private readonly RedisTagAwareAdapter $cache;

    public function __construct(private readonly CacheControl $cacheControl)
    {
        $redis = RedisConnectionService::getInstance()->getConnection();
        $this->cache = new RedisTagAwareAdapter($redis);
    }

    public function handle(Request $request, RequestHandler $requestHandler): Response
    {
        $this->init($request, $requestHandler);
        $this->loadCallable();
        $this->attributes = $this->cacheControl->loadAttributes($this->callable);
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

    private function checkCache(): void
    {
        if (empty($this->attributes)) {
            return;
        }
        $cacheKey = $this->cacheControl->getCacheKey($this->attributes[0]);
        $responseContent = $this->cache->get($cacheKey, function (ItemInterface $item): string {
            $item->expiresAfter($this->cacheControl->getExpiration($this->attributes[0]));
            $item->tag($this->cacheControl->getTags($this->attributes[0]));
            $content = $this->getResponseContent();
            if ($content === null) {
                $item->expiresAfter(0);
                $content = '';
            }
            return $content;
        });
        $this->response = $this->getResponseFromContent($responseContent);
    }

    private function getResponseContent(): ?string
    {
        $response = $this->requestHandler->handle($this->request);
        if ($response->getStatusCode() >= 200 && $response->getStatusCode() <= 299) {
            return $response->getBody()->getContents();
        }
        return null;
    }

    private function getResponseFromContent(string $content): Response
    {
        $response = AppFactory::create()->getResponseFactory()->createResponse();
        $sf = new StreamFactory();
        $stream = $sf->createStream($content);
        return $response
            ->withBody($stream)
            ->withStatus(200)
            ->withHeader('Content-Type', $this->cacheControl->getContentType($this->attributes[0]))
            ->withHeader('Content-Length', strlen($content));
    }
}
