<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

// All routes imported here inherit prefix '/api' and name prefix 'api_'.
// Dev routes additionally inherit '/api/dev' and 'api_dev_'.
return function (RoutingConfigurator $routes): void {
    foreach ([
        '/api/app/setup.php',
        '/api/admin/setup.php',
        '/api/health.php',
    ] as $file) {
        $routes->import(__DIR__ . $file)->prefix('/api')->namePrefix('api_');
    }
    $routes->import(__DIR__ . '/api/dev.php')->prefix('/api/dev')->namePrefix('api_dev_');
};
