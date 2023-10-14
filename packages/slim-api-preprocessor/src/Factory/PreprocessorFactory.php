<?php

declare(strict_types=1);

namespace SlimAP\Factory;

use SlimAP\Preprocessor\PreprocessorInterface;
use SlimAP\Registry\PreprocessorRegistry;

class PreprocessorFactory
{
    public function get(string $type, ?array $config = null): PreprocessorInterface
    {
        $preprocessor = PreprocessorRegistry::getInstance()->get($type);
        return $preprocessor::getInstance($config ?: []);
    }
}
