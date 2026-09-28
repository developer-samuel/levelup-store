<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Encryption;

interface HmacFieldDecoderContract
{
    /**
     * Decrypts a HMAC-encoded field from any object.
     *
     * @param object $object
     * @param string $field
     *
     * @return int|string|null
    */
    public function decode(object $object, string $field): int|string|null;
}
