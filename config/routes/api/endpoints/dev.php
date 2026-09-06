<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Dev\HealthCheckController;

return function (RoutingConfigurator $routes): void {
    $routes->add('api_dev_health_check', '/api/dev/health-check')
        ->controller([HealthCheckController::class, 'check'])
        ->methods(['GET']);
};
