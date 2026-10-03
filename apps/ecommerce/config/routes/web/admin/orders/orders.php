<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Admin\Order\Controller\Command\AdminOrderStatusCommandController,
    Web\Admin\Order\Controller\Query\AdminOrderQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('orders_index', '/orders')
        ->controller([AdminOrderQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('orders_show', '/orders/show/{code}')
        ->controller([AdminOrderQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('orders_status_update', '/orders/status/update')
        ->controller([AdminOrderStatusCommandController::class, 'update'])
        ->methods(['POST']);
};
