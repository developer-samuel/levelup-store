<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\User\AdminUserQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('users_index', '/users')
        ->controller([AdminUserQueryController::class, 'index'])
        ->methods(['GET']);
};
