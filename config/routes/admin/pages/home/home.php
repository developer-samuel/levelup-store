<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Home\AdminHomeQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin home page
    $routes->add('admin', '/admin')
        ->controller([AdminHomeQueryController::class, 'index'])
        ->methods(['GET']);
};
