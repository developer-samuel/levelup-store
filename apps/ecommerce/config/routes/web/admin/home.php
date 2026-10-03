<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Home\AdminHomeQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('home', '/')
        ->controller([AdminHomeQueryController::class, 'index'])
        ->methods(['GET']);
};
