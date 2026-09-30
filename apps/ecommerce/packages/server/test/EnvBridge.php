<?php

declare(strict_types=1);

namespace Packages\Test;

final class EnvBridge
{
    private const BOOT_KEYS = ['APP_ENV', 'APP_DEBUG'];
    private const OVERRIDE_KEYS = [
        'ELASTICSEARCH_ENABLED',
        'ELASTICSEARCH_HOST',
        'REDIS_URL',
        'OTEL_PHP_AUTOLOAD_ENABLED',
    ];

    /**
     * Bridge APP_ENV / APP_DEBUG into $_ENV before Symfony's bootEnv runs.
     * PHP cli-server does not populate $_ENV/$_SERVER from the process environment,
     * and bootEnv checks only $_ENV/$_SERVER for APP_ENV.
     *
     * @return void
    */
    public static function bridgeBeforeBootEnv(): void
    {
        if (PHP_SAPI !== 'cli-server') {
            return;
        }

        foreach (self::BOOT_KEYS as $key) {
            if (false !== ($val = getenv($key))) {
                $_ENV[$key] = $_SERVER[$key] = $val;
            }
        }
    }

    /**
     * Bridge playwright-specific overrides into $_ENV after bootEnv.
     * Must run after bootEnv so that DATABASE_URL (with variable references like
     * $DB_CONNECTION://...) is expanded by Symfony Dotenv first, not overwritten
     * with the unexpanded literal from Node.js dotenv in the playwright process env.
     *
     * @return void
    */
    public static function bridgeAfterBootEnv(): void
    {
        if (PHP_SAPI !== 'cli-server') {
            return;
        }

        foreach (self::OVERRIDE_KEYS as $key) {
            if (false !== ($val = getenv($key))) {
                $_ENV[$key] = $_SERVER[$key] = $val;
            }
        }
    }
}
