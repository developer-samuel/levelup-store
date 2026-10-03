<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Dashboard\AdminDashboardQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('dashboard_index', '/dashboard')
        ->controller([AdminDashboardQueryController::class, 'index'])
        ->methods(['GET']);
};
