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
        public string $stripeSecretKey,
        public string $mailerUser,
        public string $mailerPass,
        public string $mailerHost,
        public int    $mailerPort,
        public bool   $wkhtmltopdfEnabled,
        public string $wkhtmltopdfPath,
    ) {}
}
