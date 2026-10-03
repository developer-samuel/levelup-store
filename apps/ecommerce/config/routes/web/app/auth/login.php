<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Auth\Controller\Query\AuthQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('login', '/login')
        ->controller([AuthQueryController::class, 'login'])
        ->methods(['GET']);
};
