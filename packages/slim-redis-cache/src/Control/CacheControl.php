<?php

namespace SlimRC\Control;

use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Routing\RouteContext;
use SlimRC\Attribute\CacheResponse;
use SlimRC\Configuration\Configuration;
use SlimRC\Service\RedisConnectionService;
use Symfony\Component\Cache\Adapter\RedisTagAwareAdapter;

class CacheControl
{
    public function clear(array $tags): void
    {
        $redis = RedisConnectionService::getInstance()->getConnection();
        $redisAdapter = new RedisTagAwareAdapter($redis);
        $redisAdapter->invalidateTags($tags);
    }

    public function loadAttributes(string $callable): array
    {
        if (count($parts = explode(':', $callable)) === 2) {
            $class = $parts[0];
            if (class_exists($class)) {
                $reflection = new \ReflectionClass($class);
                $method = $reflection->getMethod($parts[1]);
                $attributes = $method->getAttributes(CacheResponse::class);
                return array_map(static fn($a) => $a->newInstance(), $attributes);
            }
        }
        return [];
    }

    public function getCacheKey(?CacheResponse $attr = null): string
    {
        $key = $attr && $attr->key ? $attr->key : null;
        if ($key) {
            return $this->addPrefix($key);
        }
        $key = $this->getRequestPath();
        $key = $key === '/' ? '' : $key;
        if (!$this->pathOnly()) {
            $query = $this->getRequest()->getUri()->getQuery();
            $key = "$key/$query";
        }
        $key = trim($key, '/');
        if (Configuration::getInstance()->isPathToSegments()) {
            $key = implode(Configuration::getInstance()->getKeySegmentSeparator(), explode('/', $key));
            return $this->addPrefix($key);
        }
        return $this->addPrefix(md5($key));
    }

    public function addPrefix(string $key, ?CacheResponse $attr = null): string
    {
        $prefix = $attr && $attr->prefix ? $attr->prefix : Configuration::getInstance()->getCacheRootSegment();
        return $prefix ? implode(Configuration::getInstance()->getKeySegmentSeparator(), [$prefix, $key]) : $key;
    }

    public function getExpiration(?CacheResponse $attr = null): ?int
    {
        $val = $attr && $attr->expiration ? $attr->expiration : null;
        if ($val === null) {
            $val = Configuration::getInstance()->getDefaultExpiration();
        }
        return $val;
    }

    public function getContentType(?CacheResponse $attr = null): string
    {
        return $attr && $attr->contentType ? $attr->contentType : 'application/json';
    }

    public function getTags(?CacheResponse $attr = null): array
    {
        $tags = ['slimrc'];
        if (Configuration::getInstance()->getCacheRootSegment()) {
            $tags[] = Configuration::getInstance()->getCacheRootSegment();
        }
        if ($attr && !empty($attr->tags)) {
            $tags = array_merge($tags, $attr->tags);
        }
        if (Configuration::getInstance()->isEnableTemplateTags()) {
            return $this->interpolateTags($tags);
        }
        return $tags;
    }

    public function interpolateTags(array $tags): array
    {
        $interpolatedTags = [];
        $routeParams = $this->getRouteParams();
        foreach ($tags as $tag) {
            $iTag = $tag;
            foreach ($routeParams as $name => $value) {
                $iTag = str_replace(sprintf('{%s}', $name), $value, $tag);
            }
            $interpolatedTags[] = $iTag;
        }
        return $interpolatedTags;
    }

    private function getRouteParams(): array
    {
        $routeContext = RouteContext::fromRequest($this->getRequest());
        $route = $routeContext->getRoute();
        return $route ? $route->getArguments() : [];
    }

    public function pathOnly(?CacheResponse $attr = null): string
    {
        return $attr->pathOnly ?? false;
    }

    public function getRequestPath(): string
    {
        return $this->getRequest()->getUri()->getPath();
    }

    public function getRequest(): Request
    {
        return Configuration::getInstance()->getRequest();
    }
}
