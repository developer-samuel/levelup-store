<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\User\AdminUserQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin users page
    $routes->add('admin_users_index', '/admin/users')
        ->controller([AdminUserQueryController::class, 'index'])
        ->methods(['GET']);
};
