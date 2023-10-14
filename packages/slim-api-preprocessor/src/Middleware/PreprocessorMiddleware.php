<?php

declare(strict_types=1);

namespace SlimAP\Middleware;

use SlimAP\Handler\PreprocessingRequestHandler;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Psr\Http\Message\ResponseInterface as Response;

class PreprocessorMiddleware
{
    public function __invoke(Request $request, RequestHandler $handler): Response
    {
        if (in_array(strtolower($request->getMethod()), ['post', 'put', 'patch'], true)) {
            $pHandler = new PreprocessingRequestHandler();
            $request = $pHandler->handle($request);
        }
        $originalResponse = $handler->handle($request);
        return $originalResponse;
    }
}
