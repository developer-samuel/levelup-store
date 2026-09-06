<?php

declare(strict_types=1);

namespace App\Presentation\Dev;

final readonly class HealthCheckConfig
{
    /**
     * @param string $stripeSecretKey
     * @param string $mailerUser
     * @param string $mailerPass
     * @param string $mailerHost
     * @param int $mailerPort
     * @param bool $wkhtmltopdfEnabled
     * @param string $wkhtmltopdfPath
    */
    public function __construct(
        public readonly string $stripeSecretKey,
        public readonly string $mailerUser,
        public readonly string $mailerPass,
        public readonly string $mailerHost,
        public readonly int    $mailerPort,
        public readonly bool   $wkhtmltopdfEnabled,
        public readonly string $wkhtmltopdfPath,
    ) {}
}
