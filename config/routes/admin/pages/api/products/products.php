<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\AdminApiProductQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api products list
    $routes->add('api_admin_products_list', '/api/admin/products/list')
        ->controller([AdminApiProductQueryController::class, 'list'])
        ->methods(['GET']);
};
