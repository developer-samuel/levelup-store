<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Home\HomeQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('home', '/')
        ->controller([HomeQueryController::class, 'index'])
        ->methods(['GET']);
};
