<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Order\Controller\Query\AdminApiOrderQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('orders_list', '/orders/list')
        ->controller([AdminApiOrderQueryController::class, 'list'])
        ->methods(['GET']);
};
