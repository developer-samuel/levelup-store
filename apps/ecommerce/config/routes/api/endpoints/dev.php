<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Dev\HealthCheckApiController;

return function (RoutingConfigurator $routes): void {
    $routes->import('@NelmioApiDocBundle/config/routing/swaggerui.xml')
        ->prefix('/api/dev/docs');

    $routes->add('api_dev_health_check', '/api/dev/health-check')
        ->controller([HealthCheckApiController::class, 'check'])
        ->methods(['GET']);
};
