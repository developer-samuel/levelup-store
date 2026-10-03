<?php

declare(strict_types=1);

namespace App\Presentation\Api\Dev;

final readonly class HealthCheckConfig
{
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
