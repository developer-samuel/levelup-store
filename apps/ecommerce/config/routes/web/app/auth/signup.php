<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Auth\Controller\Command\SignupCommandController,
    Web\Auth\Controller\Query\AuthQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('signup', '/signup')
        ->controller([AuthQueryController::class, 'signup'])
        ->methods(['GET']);

    $routes->add('signup_store', '/signup/store')
        ->controller([SignupCommandController::class, 'store'])
        ->methods(['POST']);
};
