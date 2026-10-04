<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Encryption;

use App\Core\Ports\{
    Shared\Encryption\HmacFieldDecoderContract,
    Shared\Encryption\HmacGeneratorContract
};

final readonly class HmacFieldDecoder implements HmacFieldDecoderContract
{
    public function __construct(
        private HmacGeneratorContract $hmacGenerator,
    ) {}

    public function decode(object $object, string $field): int|string|null
    {
        $data = get_object_vars($object);
        if (!isset($data[$field])) {
            return null;
        }

        return $this->decodeValue($data[$field]);
    }

    private function decodeValue(mixed $value): int|string|null
    {
        if (is_int($value)) {
            $value = (string) $value;
        }

        if (!is_string($value) || $value === '') {
            return null;
        }

        $decrypted = $this->hmacGenerator->decrypt($value);
        if ($decrypted === null || $decrypted === '') {
            return null;
        }

        return $decrypted;
    }
}
