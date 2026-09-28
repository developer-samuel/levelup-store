<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Segment\Cart\Controller\CartCommandController;

return function (RoutingConfigurator $routes) {
    // Route for storing items in the cart
    $routes->add('cart_store', '/cart/store')
        ->controller([CartCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for destroying the cart
    $routes->add('cart_destroy', '/cart/destroy')
        ->controller([CartCommandController::class, 'destroy'])
        ->methods(['POST']);
};
