<?php

namespace SlimRC\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use SlimRC\Configuration\Configuration;
use SlimRC\Handler\RouteCacheHandler;

class RedisCacheMiddleware
{
    public function __construct(private readonly RouteCacheHandler $routeCacheHandler)
    {
    }

    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        Configuration::getInstance()->setRequest($request);
        if (strtolower($request->getMethod()) === 'get') {
            $response = $this->routeCacheHandler->handle($request, $handler);
        } else {
            $response = $handler->handle($request);
        }
        return $response;
    }
}
