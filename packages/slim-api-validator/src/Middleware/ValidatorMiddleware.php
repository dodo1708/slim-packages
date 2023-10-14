<?php

namespace SlimAV\Middleware;

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;
use SlimAV\Handler\RouteValidationHandler;

class ValidatorMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (in_array(strtolower($request->getMethod()), ['post', 'put', 'patch'], true)) {
            $vHandler = new RouteValidationHandler();
            $vHandler->handle($request);
        }
        $originalResponse = $handler->handle($request);
        return $originalResponse;
    }
}
