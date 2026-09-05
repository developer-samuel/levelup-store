<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Order\Controller\Query\AdminApiOrderHistoryQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api orders list
    $routes->add('api_admin_orders_history_list', '/api/admin/orders/history/list')
        ->controller([AdminApiOrderHistoryQueryController::class, 'list'])
        ->methods(['GET']);
};
