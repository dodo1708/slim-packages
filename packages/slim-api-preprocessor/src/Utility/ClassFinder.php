<?php

declare(strict_types=1);

namespace SlimAP\Utility;

class ClassFinder
{
    //This value should be the directory that contains composer.json
    final const appRoot = __DIR__ . "/../../";

    public static function getClassesInNamespace(string $namespace)
    {
        $files = scandir(self::getNamespaceDirectory($namespace));

        $classes = array_map(function ($file) use ($namespace) {
            return $namespace . '\\' . str_replace('.php', '', $file);
        }, $files);

        return array_filter($classes, fn ($possibleClass) => class_exists($possibleClass));
    }

    private static function getDefinedNamespaces()
    {
        $composerJsonPath = self::appRoot . 'composer.json';
        $composerConfig = json_decode(file_get_contents($composerJsonPath));

        return (array) $composerConfig->autoload->{'psr-4'};
    }

    private static function getNamespaceDirectory(string $namespace)
    {
        $composerNamespaces = self::getDefinedNamespaces();

        $namespaceFragments = explode('\\', $namespace);
        $undefinedNamespaceFragments = [];

        while ($namespaceFragments) {
            $possibleNamespace = implode('\\', $namespaceFragments) . '\\';

            if (array_key_exists($possibleNamespace, $composerNamespaces)) {
                return realpath(self::appRoot . $composerNamespaces[$possibleNamespace] . implode('/', $undefinedNamespaceFragments));
            }

            array_unshift($undefinedNamespaceFragments, array_pop($namespaceFragments));
        }

        return false;
    }
}
