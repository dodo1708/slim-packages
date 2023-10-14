<?php

declare(strict_types=1);

namespace SlimAP\Registry;

use SlimAP\Exception\SlimAPException;
use SlimAP\Preprocessor\AbstractPreprocessor;
use SlimAP\Preprocessor\PreprocessorInterface;
use SlimAP\Utility\Utility;

final class PreprocessorRegistry
{
    private static ?PreprocessorRegistry $instance = null;

    private array $registry = [];

    public static function getInstance(): PreprocessorRegistry
    {
        if (!isset(self::$instance)) {
            self::$instance = new PreprocessorRegistry();
        }
        return self::$instance;
    }

    public function add(PreprocessorInterface $preprocessor): void
    {
        if (isset($this->registry[$preprocessor::getIdentifier()])) {
            throw new SlimAPException("A preprocessor with this identifier already exists: '{$preprocessor::getIdentifier()}'");
        }
        $this->registry[$preprocessor::getIdentifier()] = $preprocessor;
    }

    public function get(string $type): PreprocessorInterface
    {
        if (!isset($this->registry[$type])) {
            throw new SlimAPException("A preprocessor with this identifier does not exists: '{$type}'");
        }
        return $this->registry[$type];
    }

    public function loadDefaults(): void
    {
        $preprocessorClasses = Utility::classesInNamespace(
            'SlimAP\Preprocessor',
            [AbstractPreprocessor::class, PreprocessorInterface::class]
        );
        foreach ($preprocessorClasses as $preprocessorClass) {
            $this->add(new $preprocessorClass());
        }
    }
}
