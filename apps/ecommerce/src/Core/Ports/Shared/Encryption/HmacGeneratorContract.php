<?php

declare(strict_types=1);

namespace App\Core\Ports\Shared\Encryption;

interface HmacGeneratorContract
{
    /**
     * @param int $value
     *
     * @return string
    */
    public function encrypt(int $value): string;

    /**
     * @param string $encoded
     *
     * @return int|string|null
    */
    public function decrypt(string $encoded): int|string|null;
}
