<?php

namespace App\Helper;

final class GenerationHelper
{
    public static function createOutputFolder(string $folderPath)
    {
        @mkdir($folderPath, 0777, true);
    }

    public static function getOutputFolder(string $namespace): string
    {
        return (str_replace('.', '/', str_replace('com.ankamagames.dofus.network.', '', $namespace)) . '/');
    }

    public static function getOutputPath(string $namespace, string $fileName): string
    {
        $folderPath = OUTPUT_FOLDER . '/' . self::getOutputFolder($namespace);

        self::createOutputFolder($folderPath);

        return ($folderPath . '/' . $fileName . LANGUAGE_EXTENSION);
    }
}