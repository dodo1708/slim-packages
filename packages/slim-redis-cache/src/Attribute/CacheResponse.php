<?php

namespace SlimRC\Attribute;

use Attribute;

#[Attribute]
class CacheResponse
{
    /**
     * @param $key : Optional. The cache key to use for the route result. If omitted, an md5 hash of the route (+ query params) will be the cache key.
     * @param $prefix : Optional. Overwrite the globally configured cache prefix.
     * @param $tags : Optional. A list of tags for the cache item.
     * @param $pathOnly : Optional. Ignore query params when caching the response data.
     * @param $expiration : Optional. Expiration time of the cached data. If omitted, response data will be cached forever.
     */
    public function __construct(
        public ?string $key = null,
        public ?string $prefix = null,
        public array $tags = [],
        public string $contentType = 'application/json',
        public bool $pathOnly = false,
        public ?int $expiration = null
    ) {
    }
}
