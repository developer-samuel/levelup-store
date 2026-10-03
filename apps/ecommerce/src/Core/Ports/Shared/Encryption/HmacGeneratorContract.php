<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Encryption;

interface HmacGeneratorContract
{
    public function encrypt(int $value): string;
    public function decrypt(string $encoded): int|string|null;
}
