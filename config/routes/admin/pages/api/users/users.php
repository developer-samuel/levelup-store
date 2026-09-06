<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\User\AdminApiUserQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api users list
    $routes->add('api_admin_users_list', '/api/admin/users/list')
        ->controller([AdminApiUserQueryController::class, 'list'])
        ->methods(['GET']);
};
