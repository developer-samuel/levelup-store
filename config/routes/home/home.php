<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Home\HomeQueryController;

return function (RoutingConfigurator $routes) {
    // Route for home page
    $routes->add('home', '/')
        ->controller([HomeQueryController::class, 'index'])
        ->methods(['GET']);
};
