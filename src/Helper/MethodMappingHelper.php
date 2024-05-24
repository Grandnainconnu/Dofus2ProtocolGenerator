<?php

namespace App\Helper;

final class MethodMappingHelper
{
    public static function getMethod(string $key, array $object): ?string
    {
        return isset($object[$key]) ? 
            WRITE_METHOD_OVERRIDE[$object[$key]] ?? $object[$key] : 
            null
        ;
    }

    public static function getReadingMethod(?string $writingMethod): ?string
    {
        return READ_METHOD_MAPPER[$writingMethod] ?? null;
    }
}