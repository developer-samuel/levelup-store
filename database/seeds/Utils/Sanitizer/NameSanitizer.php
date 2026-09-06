<?php

declare(strict_types=1);

namespace Database\Seeds\Utils\Sanitizer;

use Kit\Utils\Shared\DataSanitizer;

trait NameSanitizer
{
    /**
     * @param mixed $data
     *
     * @return string
     *
     * @throws \InvalidArgumentException
    */
    protected function sanitize(mixed $data): string
    {
        $name = match (true) {
            is_string($data)                                                    => $data,
            is_array($data) && isset($data['name']) && is_string($data['name']) => $data['name'],
            default                                                             => throw new \InvalidArgumentException(
                sprintf('Expected string or array with "name" key, got %s.', get_debug_type($data)),
            ),
        };

        return DataSanitizer::sanitizeString($name);
    }
}
