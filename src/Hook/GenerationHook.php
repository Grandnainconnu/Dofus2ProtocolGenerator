<?php

namespace App\Hook;

use Twig\{
    Loader\FilesystemLoader,
    Environment
};

class GenerationHook
{
    /**
     * @var Environment
     */
    private static $twig;

    /**
     * @var array
     */
    private static $filters = [];

    /**
     * @var array
     */
    private static $globalFilters = [];

    public static function initializeTwig($template): void
    {
        $loader = new FilesystemLoader(ROOT . '/templates/' . $template);
        $twig = new Environment($loader);

        self::$twig = $twig;
    }

    public static function addFilter(callable $filter, bool $isGlobal = false): void
    {
        if ($isGlobal === false) {
            self::$filters[] = $filter;
        } else {
            self::$globalFilters[] = $filter;
        }
    }

    public static function executeFilters(array &$objects, string $group): void
    {
        foreach (self::$globalFilters as $filter) {
            $filter($objects, $group);
        }

        foreach (self::$filters as $filter) {
            foreach ($objects as $objectKey => &$object) {
                if ($filter($object, $group) === false) {
                    unset($objects[$objectKey]);
                }
            }
        }
    }

    public static function getTwig(): Environment
    {
        return self::$twig;
    }

    public static function getFilters(): array
    {
        return self::$filters;
    }

    public static function getGlobalFilters(): array
    {
        return self::$globalFilters;
    }
}
