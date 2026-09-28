<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Utils;

final class ResponseFormatter
{
    /**
     * @param string|null $message
     * @param array<string, mixed> $data
     *
     * @return array<string, mixed>
    */
    public static function success(?string $message = null, array $data = []): array
    {
        return array_merge([
            'success' => true,
            'message' => $message,
        ], $data);
    }

    /**
     * @param string|null $message
     * @param array<string, mixed> $errors
     * @param int|null $status
     *
     * @return array<string, mixed>
    */
    public static function errors(?string $message = null, array $errors = [], ?int $status = null): array
    {
        return [
            'success' => false,
            'message' => $message ?? null,
            'errors'  => $errors !== [] ? $errors : null,
            'status'  => $status,
        ];
    }
}
