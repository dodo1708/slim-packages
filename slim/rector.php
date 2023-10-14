<?php

declare(strict_types=1);

use Rector\Config\RectorConfig;
use Rector\Set\ValueObject\SetList;

return static function (RectorConfig $rectorConfig): void {
    $rectorConfig->paths([
        __DIR__ . '/app',
        __DIR__ . '/public',
        __DIR__ . '/src',
    ]);

    $rectorConfig->import(SetList::PHP_80);
    $rectorConfig->import(SetList::PHP_81);
};
