<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Order\Controller\Query\AdminOrderHistoryQueryController;

return function (RoutingConfigurator $routes) { 
    // Route for admin orders page
    $routes->add('admin_orders_history_index', '/admin/orders/history')
        ->controller([AdminOrderHistoryQueryController::class, 'index'])
        ->methods(['GET']);
};
