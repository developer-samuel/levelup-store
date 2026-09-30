<?php

declare(strict_types=1);

namespace App\Core\Application\Shared\Encryption;

use App\Core\Ports\Shared\Encryption\HmacGeneratorContract;

final readonly class HmacGenerator implements HmacGeneratorContract
{
    private const HMAC_ALGO = 'sha256';

    /**
     * @param string $secretKey
    */
    public function __construct(
        private string $secretKey,
    ) {}

    /**
     * @param int $value
     *
     * @return string
    */
    public function encrypt(int $value): string
    {
        $rawValue = (string) $value;
        $signature = $this->generateHmac($value);

        return $this->encodePayload(['value' => $rawValue, 'hmac' => $signature]);
    }

    /**
     * @param string $encoded
     *
     * @return int|string|null
    */
    public function decrypt(string $encoded): int|string|null
    {
        $decoded = $this->decodePayload($encoded);
        if ($decoded === null || !$this->isValidPayload($decoded)) {
            return null;
        }

        return $this->validateAndReturnValue($decoded);
    }

    /**
     * @param int|string $value
     *
     * @return string
    */
    private function generateHmac(int|string $value): string
    {
        return hash_hmac(self::HMAC_ALGO, (string) $value, $this->secretKey);
    }

    /**
     * @param string[] $payload
     *
     * @return string
    */
    private function encodePayload(array $payload): string
    {
        $json = json_encode($payload, JSON_THROW_ON_ERROR);

        return base64_encode($json);
    }

    /**
     * @param string $encoded
     *
     * @return string[]|null
    */
    private function decodePayload(string $encoded): ?array
    {
        $json = base64_decode($encoded, true);
        if ($json === false) {
            return null;
        }

        return $this->parseAndValidateJson($json);
    }

    /**
     * @param string $json
     *
     * @return string[]|null
    */
    private function parseAndValidateJson(string $json): ?array
    {
        try {
            $decoded = json_decode($json, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($decoded)) {
                return null;
            }

            return $this->ensureStringMap($decoded);
        } catch (\JsonException) {
            return null;
        }
    }

    /**
     * @param array<mixed> $data
     *
     * @return string[]|null
    */
    private function ensureStringMap(array $data): ?array
    {
        $validated = [];

        foreach ($data as $key => $value) {
            if (!is_string($key) || !is_string($value)) {
                return null;
            }

            $validated[$key] = $value;
        }

        return $validated;
    }

    /**
     * @param string[] $decoded
     *
     * @return bool
    */
    private function isValidPayload(array $decoded): bool
    {
        return isset($decoded['value'], $decoded['hmac'])
            && hash_equals($this->generateHmac($decoded['value']), $decoded['hmac']);
    }

    /**
     * @param string[] $decoded
     *
     * @return int|string
    */
    private function validateAndReturnValue(array $decoded): int|string
    {
        $value = $decoded['value'];

        if (ctype_digit($value)) {
            return (int) $value;
        }

        return $value;
    }
}
