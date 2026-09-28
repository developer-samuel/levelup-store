<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Segment\Order\Controller\Query\OrderStatusQueryController;

return function (RoutingConfigurator $routes) {
    // Route for order success status
    $routes->add('orders_success', '/orders/success')
        ->controller([OrderStatusQueryController::class, 'success'])
        ->methods(['GET']);

    // Route for order cancel status
    $routes->add('orders_cancel', '/orders/cancel')
        ->controller([OrderStatusQueryController::class, 'cancel'])
        ->methods(['GET']);

    // Route for order error status
    $routes->add('orders_error', '/orders/error')
        ->controller([OrderStatusQueryController::class, 'error'])
        ->methods(['GET']);
};
