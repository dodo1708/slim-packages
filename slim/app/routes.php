<?php

declare(strict_types=1);

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;

return function (App $app) {
    $app->options('/{routes:.*}', function (Request $request, Response $response) {
        // CORS Pre-Flight OPTIONS Request Handler
        return $response;
    });

    $app->get(
        '/clear-test1',
        \App\Application\Controller\DefaultController::class . ':clear'
    );

    $app->get(
        '/other',
        \App\Application\Controller\DefaultController::class . ':other'
    );

    $app->get(
        '/[{path:.*}]',
        \App\Application\Controller\DefaultController::class . ':default'
    );
};
