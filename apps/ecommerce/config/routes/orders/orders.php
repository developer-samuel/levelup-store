<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Segment\Order\Controller\Command\OrderCommandController,
    Segment\Order\Controller\Query\OrderQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for listing all orders
    $routes->add('orders_index', '/orders')
        ->controller([OrderQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for viewing details of a specific order by code
    $routes->add('orders_show', '/orders/show/{code}')
        ->controller([OrderQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for creating an order
    $routes->add('orders_create', '/orders/create')
        ->controller([OrderQueryController::class, 'create'])
        ->methods(['GET']);

    // Route for storing the created order
    $routes->add('orders_store', '/orders/store')
        ->controller([OrderCommandController::class, 'store'])
        ->methods(['POST']);
};
