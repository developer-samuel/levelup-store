<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Dev\HealthCheckApiController;

return function (RoutingConfigurator $routes): void {
    if (in_array($routes->env(), ['local', 'dev', 'test'], true)) {
        $routes->import('@NelmioApiDocBundle/config/routing/swaggerui.xml')
            ->prefix('/docs');

        $routes->add('health_check', '/health-check')
            ->controller([HealthCheckApiController::class, 'check'])
            ->methods(['GET']);
    }
};
