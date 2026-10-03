<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Auth\AuthApiCommandController;

return function (RoutingConfigurator $routes): void {
    $routes->add('auth_login', '/auth/login')
        ->controller([AuthApiCommandController::class, 'login'])
        ->methods(['POST']);

    $routes->add('auth_refresh', '/auth/refresh')
        ->controller([AuthApiCommandController::class, 'refresh'])
        ->methods(['POST']);

    $routes->add('auth_logout', '/auth/logout')
        ->controller([AuthApiCommandController::class, 'logout'])
        ->methods(['POST']);
};
