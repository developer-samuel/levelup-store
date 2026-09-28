<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Http;

use Symfony\Component\HttpFoundation\RequestStack;

final readonly class RequestMetadata
{
    /**
     * @param string $ip
     * @param string $userAgent
    */
    public function __construct(
        public string $ip,
        public string $userAgent,
    ) {}

    /**
     * @param RequestStack $requestStack
     *
     * @return string
    */
    private static function resolveIp(RequestStack $requestStack): string
    {
        return $requestStack->getCurrentRequest()?->getClientIp() ?? 'unknown';
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return string
    */
    private static function resolveUserAgent(RequestStack $requestStack): string
    {
        return $requestStack->getCurrentRequest()?->headers->get('User-Agent') ?? 'unknown';
    }

    /**
     * @param RequestStack $requestStack
     *
     * @return self
    */
    public static function fromRequestStack(RequestStack $requestStack): self
    {
        return new self(
            ip:        self::resolveIp($requestStack),
            userAgent: self::resolveUserAgent($requestStack),
        );
    }
}
