<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Encryption;

interface HmacFieldDecoderContract
{
    public function decode(object $object, string $field): int|string|null;
}
