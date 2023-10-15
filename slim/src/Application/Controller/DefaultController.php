<?php

declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\RequestInterface;
use Slim\Http\Response;
use SlimRC\Attribute\CacheResponse;
use SlimRC\Control\CacheControl;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DefaultController
{
    #[CacheResponse(null, null, ['test1'])]
    public function default(RequestInterface $request, Response $response, array $args): Response
    {
        $loader = new FilesystemLoader('/app/slim/templates');
        $twig = new Environment($loader);
        $response->getBody()->write(
            $twig->render(
                'index.html.twig',
                [
                    'language' => 'de',
                    'title' => '---',
                    'context' => getenv('CONTEXT'),
                    'domain' => getenv('DOMAIN'),
                ]
            )
        );
        return $response;
    }

    #[CacheResponse(null, null, ['other'])]
    public function other(RequestInterface $request, Response $response, array $args): Response
    {
        $response->getBody()->write('Other');
        return $response;
    }

    public function clear(RequestInterface $request, Response $response, array $args): Response
    {
        $cacheControl = new CacheControl();
        $cacheControl->clear(['test1']);
        $response->getBody()->write('Cleared');
        return $response;
    }
}
