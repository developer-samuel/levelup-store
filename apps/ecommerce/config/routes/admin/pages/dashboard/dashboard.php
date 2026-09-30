<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Dashboard\AdminDashboardQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin dashboard page
    $routes->add('admin_dashboard_index', '/admin/dashboard')
        ->controller([AdminDashboardQueryController::class, 'index'])
        ->methods(['GET']);
};
