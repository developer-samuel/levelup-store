<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Home\AdminHomeQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('admin_home', '/admin')
        ->controller([AdminHomeQueryController::class, 'index'])
        ->methods(['GET']);
};
