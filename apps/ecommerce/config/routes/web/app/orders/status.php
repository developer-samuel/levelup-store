<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Segment\Order\Controller\Query\OrderStatusQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('orders_success', '/orders/success')
        ->controller([OrderStatusQueryController::class, 'success'])
        ->methods(['GET']);

    $routes->add('orders_cancel', '/orders/cancel')
        ->controller([OrderStatusQueryController::class, 'cancel'])
        ->methods(['GET']);

    $routes->add('orders_error', '/orders/error')
        ->controller([OrderStatusQueryController::class, 'error'])
        ->methods(['GET']);
};
