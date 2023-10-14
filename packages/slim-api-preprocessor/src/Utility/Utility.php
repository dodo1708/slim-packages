<?php

declare(strict_types=1);

namespace SlimAP\Utility;

class Utility
{
    public static function classesInNamespace(string $namespace, ?array $ignore = null): array
    {
        $ignoreIndex = !empty($ignore) ? array_flip($ignore) : null;
        $myClasses = ClassFinder::getClassesInNamespace($namespace);
        $theClasses = [];
        foreach ($myClasses as $class) {
            if (!isset($ignoreIndex[$class])) {
                $theClasses[] = $class;
            }
        }
        return $theClasses;
    }
}
