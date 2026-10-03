<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Order\Controller\Query\AdminApiOrderHistoryQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('orders_history_list', '/orders/history/list')
        ->controller([AdminApiOrderHistoryQueryController::class, 'list'])
        ->methods(['GET']);
};
