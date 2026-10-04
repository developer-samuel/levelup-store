<?php

declare(strict_types=1);

namespace App\Core\Application\Cookie;

use App\Core\Domain\Cookie\CookieObject;

use App\Core\Application\Shared\Constants\CookieConstants;

final class CookieFactory
{
    public function fromObject(): CookieObject
    {
        $expires = $this->calculateExpirationTime();

        return new CookieObject(
            CookieConstants::NAME,
            CookieConstants::VALUE,
            $expires,
            CookieConstants::PATH,
            CookieConstants::SECURE,
            CookieConstants::HTTP_ONLY,
        );
    }

    private function calculateExpirationTime(): int
    {
        $timestamp = strtotime(CookieConstants::DURATION);
        if ($timestamp === false) {
            return $this->defaultDuration();
        }

        return $timestamp;
    }

    private function defaultDuration(): int
    {
        return time() + (365 * 24 * 3600);
    }
}
