<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Segment\Order\Controller\Command\OrderCommandController,
    Web\Segment\Order\Controller\Query\OrderQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('orders_index', '/orders')
        ->controller([OrderQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('orders_show', '/orders/show/{code}')
        ->controller([OrderQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('orders_create', '/orders/create')
        ->controller([OrderQueryController::class, 'create'])
        ->methods(['GET']);

    $routes->add('orders_store', '/orders/store')
        ->controller([OrderCommandController::class, 'store'])
        ->methods(['POST']);
};
