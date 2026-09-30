<?php

declare(strict_types=1);

namespace App\Shared\Utils\Formatter;

/**
 * @phpstan-type ApiSuccessResult array{
 *     status: 'success',
 *     message: string,
 *     data?: array<string, mixed>,
 *     redirect?: string
 * }
 * @phpstan-type ApiErrorResult array{
 *     status: 'error',
 *     message: string,
 *     code: int,
 *     errors?: array<string, mixed>
 * }
*/
final class ApiResultFormatter
{
    /**
     * @param string $message
     * @param array<string, mixed>|null $data
     * @param string|null $redirect
     *
     * @return ApiSuccessResult
    */
    public static function success(
        string $message,
        ?array $data = null,
        ?string $redirect = null,
    ): array {
        $response = [
            'status'  => 'success',
            'message' => $message,
        ];

        if ($data !== null) {
            $response['data'] = $data;
        }

        if ($redirect !== null) {
            $response['redirect'] = $redirect;
        }

        return $response;
    }

    /**
     * @param int $code
     * @param string $message
     * @param array<string, mixed>|null $errors
     *
     * @return ApiErrorResult
    */
    public static function error(
        int $code,
        string $message,
        ?array $errors = null,
    ): array {
        $response = [
            'status'  => 'error',
            'message' => $message,
            'code'    => $code,
        ];

        if ($errors !== null) {
            $response['errors'] = $errors;
        }

        return $response;
    }
}
