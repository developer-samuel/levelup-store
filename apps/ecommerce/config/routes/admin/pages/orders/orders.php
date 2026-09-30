<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Admin\Feature\Order\Controller\Command\AdminOrderStatusCommandController,
    Admin\Feature\Order\Controller\Query\AdminOrderQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for admin orders page
    $routes->add('admin_orders_index', '/admin/orders')
        ->controller([AdminOrderQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for admin orders list
    $routes->add('admin_orders_show', '/admin/orders/show/{code}')
        ->controller([AdminOrderQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for admin order status update
    $routes->add('admin_orders_status_update', '/admin/orders/status/update')
        ->controller([AdminOrderStatusCommandController::class, 'update'])
        ->methods(['POST']);
};
