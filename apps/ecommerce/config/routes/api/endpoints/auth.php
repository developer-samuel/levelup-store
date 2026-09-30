<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Auth\Controller\Command\AuthApiCommandController;

return function (RoutingConfigurator $routes): void {
    // JWT auth routes
    $routes->add('api_auth_login', '/api/auth/login')
        ->controller([AuthApiCommandController::class, 'login'])
        ->methods(['POST']);

    $routes->add('api_auth_refresh', '/api/auth/refresh')
        ->controller([AuthApiCommandController::class, 'refresh'])
        ->methods(['POST']);

    $routes->add('api_auth_logout', '/api/auth/logout')
        ->controller([AuthApiCommandController::class, 'logout'])
        ->methods(['POST']);
};
