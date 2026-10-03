<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\User\AdminApiUserQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('users_list', '/users/list')
        ->controller([AdminApiUserQueryController::class, 'list'])
        ->methods(['GET']);
};
