<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Utils;

use App\Core\Ports\Shared\Encryption\HmacFieldDecoderContract;

final class IdDecoder
{
    /**
     * @param HmacFieldDecoderContract $decoder
     * @param object $request
     * @param string $paramName
     *
     * @return int
     *
     * @throws \RuntimeException
    */
    public static function decode(HmacFieldDecoderContract $decoder, object $request, string $paramName): int
    {
        $decoded = $decoder->decode($request, $paramName);

        $isValid = match (true) {
            is_int($decoded)    => $decoded > 0,
            is_string($decoded) => ctype_digit($decoded) && (int) $decoded > 0,
            default             => false,
        };

        if (!$isValid) {
            $friendlyName = self::camelCaseToWords($paramName);
            throw new \RuntimeException(sprintf('Invalid %s.', $friendlyName));
        }

        return (int) $decoded;
    }

    /**
     * @param string $input
     *
     * @return string
    */
    private static function camelCaseToWords(string $input): string
    {
        $result = preg_replace('/([a-z])([A-Z])/', '$1 $2', $input);

        return strtolower(trim($result ?? ''));
    }
}
