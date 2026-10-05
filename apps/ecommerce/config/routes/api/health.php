<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\HealthApiController;

return function (RoutingConfigurator $routes): void {
    $routes->add('health', '/health')
        ->controller([HealthApiController::class, 'health'])
        ->methods(['GET']);
};
