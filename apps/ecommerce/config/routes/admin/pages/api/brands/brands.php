<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Brand\AdminApiBrandQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api orders list
    $routes->add('api_admin_brands_list', '/api/admin/brands/list')
        ->controller([AdminApiBrandQueryController::class, 'list'])
        ->methods(['GET']);
};
