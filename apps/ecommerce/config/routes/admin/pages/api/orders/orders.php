<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Order\Controller\Query\AdminApiOrderQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api orders list
    $routes->add('api_admin_orders_list', '/api/admin/orders/list')
        ->controller([AdminApiOrderQueryController::class, 'list'])
        ->methods(['GET']);
};
