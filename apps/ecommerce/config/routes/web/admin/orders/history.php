<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Order\Controller\Query\AdminOrderHistoryQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('orders_history_index', '/orders/history')
        ->controller([AdminOrderHistoryQueryController::class, 'index'])
        ->methods(['GET']);
};
