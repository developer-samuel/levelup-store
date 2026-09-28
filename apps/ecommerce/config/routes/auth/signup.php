<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Auth\Controller\Command\SignupCommandController,
    Auth\Controller\Query\AuthQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for signup page
    $routes->add('signup', '/signup')
        ->controller([AuthQueryController::class, 'signup'])
        ->methods(['GET']);

    // Route for storing signup data
    $routes->add('signup_store', '/signup/store')
        ->controller([SignupCommandController::class, 'store'])
        ->methods(['POST']);
};
