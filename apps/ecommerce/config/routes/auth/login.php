<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Auth\Controller\Query\AuthQueryController;

return function (RoutingConfigurator $routes) {
    // Route for login page
    $routes->add('login', '/login')
        ->controller([AuthQueryController::class, 'login'])
        ->methods(['GET']);
};
