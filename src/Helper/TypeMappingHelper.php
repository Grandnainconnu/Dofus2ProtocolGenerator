<?php

namespace App\Helper;

final class TypeMappingHelper
{
    public static function getBasicType(string $rawBasicType): ?string
    {
        return BASIC_TYPE_MAPPER[$rawBasicType] ?? $rawBasicType;
    }

    public static function getTypeByWritingMethod(?string $writingMethod, string $rawType): array
    {
        $output = [
            'type' => null,
            'isObjectType' => false
        ];

        foreach (WRITE_TYPE_MAPPER as $type => $rules) {
            if (in_array($writingMethod, $rules) === true) {
                $output['type'] = $type;

                break;
            }
        }

        if ($output['type'] === null) {
            $output['type'] = self::getBasicType($rawType);

            if ($output['type'] === $rawType) {
                $output['isObjectType'] = true;
            }
        }

        return $output;
    }
}