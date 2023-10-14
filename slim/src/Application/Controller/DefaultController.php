<?php

declare(strict_types=1);

namespace App\Application\Controller;

use Psr\Http\Message\RequestInterface;
use Slim\Http\Response;
use SlimRC\Attribute\CacheResponse;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;

class DefaultController
{
    #[CacheResponse]
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
}
