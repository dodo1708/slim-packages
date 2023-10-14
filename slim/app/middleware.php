<?php

declare(strict_types=1);

use Slim\App;
use SlimAP\Middleware\PreprocessorMiddleware;
use SlimRC\Middleware\RedisCacheMiddleware;
use SlimAV\Middleware\ValidatorMiddleware;

return function (App $app) {
    $app->add(RedisCacheMiddleware::class);
    $app->add(ValidatorMiddleware::class);
    $app->add(PreprocessorMiddleware::class);
};
