<?php

declare(strict_types=1);

namespace SlimRC\Service;

use Redis;
use RedisException;

class RedisConnectionService
{
    private static ?RedisConnectionService $connectionService = null;
    private readonly Redis $connection;
    private readonly string $redisHost;
    private readonly int $redisPort;

    /**
     * @throws RedisException
     */
    private function __construct()
    {
        $redisHost = getenv('REDIS_HOST');
        $redisPort = getenv('REDIS_PORT');
        if (!$redisHost || !$redisPort) {
            throw new RedisException('Missing Redis credentials.', 1599731163);
        }
        $this->connection = new Redis();
        $this->redisHost = $redisHost;
        $this->redisPort = (int)$redisPort;
    }

    /**
     * @throws RedisException
     */
    public function connect(): void
    {
        $maxRetries = 10;
        $retries = 0;
        while (!$this->connection->isConnected() && $retries < $maxRetries) {
            try {
                $this->connection->connect(
                    $this->redisHost,
                    $this->redisPort
                );
            } catch (RedisException) {
                $retries++;
                sleep(1);
            }
        }
        if ($retries >= $maxRetries && !$this->connection->isConnected()) {
            throw new RedisException('Could not connect to redis instance.');
        }
    }

    /**
     * @throws RedisException
     */
    public function checkConnection(): void
    {
        try {
            $this->connection->ping();
        } catch (RedisException) {
            $this->connect();
        }
    }

    /**
     * @throws RedisException
     */
    public static function getInstance(): self
    {
        if (self::$connectionService === null) {
            self::$connectionService = new RedisConnectionService();
        }

        return self::$connectionService;
    }

    /**
     * @throws RedisException
     */
    public function getConnection(): Redis
    {
        if (!$this->connection->isConnected()) {
            $this->connect();
        }
        $this->checkConnection();
        return $this->connection;
    }
}
