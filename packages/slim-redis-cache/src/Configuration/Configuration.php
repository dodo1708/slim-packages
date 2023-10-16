<?php

namespace SlimRC\Configuration;

use Psr\Http\Message\ServerRequestInterface as Request;

final class Configuration
{
    private static ?Configuration $instance = null;

    /**
     * Instead of computing a hash based on the current route, transform the URL path to redis key segments:
     * /foo/bar -> foo#bar
     */
    private bool $pathToSegments = false;
    /**
     * Allows interpolating tags with route params: some-tag-{id} + /some/route/{id:[0-9]+} -> /some/route/123 -> some-tag-123
     */
    private bool $enableTemplateTags = true;
    /**
     * Prefix all cache entries with the given prefix.
     * foo -> foo#35a63c8a85b1279a0f991ce8828fb9d9
     */
    private string $cacheRootSegment = '';
    private ?int $defaultExpiration = 60 * 60 * 24;
    private string $keySegmentSeparator = '#';
    private string $redisHost = 'redis';
    private int $redisPort = 6379;
    private Request $request;

    private function __construct()
    {
    }

    public static function getInstance(): Configuration
    {
        if (!Configuration::$instance) {
            Configuration::$instance = new static();
        }
        return Configuration::$instance;
    }

    public function isPathToSegments(): bool
    {
        return $this->pathToSegments;
    }

    public function setPathToSegments(bool $pathToSegments): void
    {
        $this->pathToSegments = $pathToSegments;
    }

    public function getCacheRootSegment(): string
    {
        return $this->cacheRootSegment;
    }

    public function setCacheRootSegment(string $cacheRootSegment): void
    {
        $this->cacheRootSegment = $cacheRootSegment;
    }

    public function getDefaultExpiration(): ?int
    {
        return $this->defaultExpiration;
    }

    public function setDefaultExpiration(?int $defaultExpiration): void
    {
        $this->defaultExpiration = $defaultExpiration;
    }

    public function getKeySegmentSeparator(): string
    {
        return $this->keySegmentSeparator;
    }

    public function setKeySegmentSeparator(string $keySegmentSeparator): void
    {
        $this->keySegmentSeparator = $keySegmentSeparator;
    }

    public function getRequest(): Request
    {
        return $this->request;
    }

    public function setRequest(Request $request): void
    {
        $this->request = $request;
    }

    public function isEnableTemplateTags(): bool
    {
        return $this->enableTemplateTags;
    }

    public function setEnableTemplateTags(bool $enableTemplateTags): void
    {
        $this->enableTemplateTags = $enableTemplateTags;
    }

    public function getRedisHost(): string
    {
        return $this->redisHost ?: getenv('REDIS_HOST') ?: '';
    }

    public function setRedisHost(string $redisHost): void
    {
        $this->redisHost = $redisHost;
    }

    public function getRedisPort(): int
    {
        return $this->redisPort ?: (int)(getenv('REDIS_PORT') ?: '6379');
    }

    public function setRedisPort(int $redisPort): void
    {
        $this->redisPort = $redisPort;
    }
}
