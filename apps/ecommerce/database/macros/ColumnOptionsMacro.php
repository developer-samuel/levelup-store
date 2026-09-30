<?php

declare(strict_types=1);

namespace Database\Macros;

final class ColumnOptionsMacro
{
    /**
     * Merge default options with the provided ones.
     * 
     * @param array<string, bool|int|string> $options
     * @param int|null $default
     *
     * @return array<string, bool|int|string>
    */
    public static function mergeOptions(array $options, ?int $default = 0): array
    {
        if (!array_key_exists('default', $options)) {
            $options['default'] = $default ?? 0;
        }
        
        return $options;
    }
}
