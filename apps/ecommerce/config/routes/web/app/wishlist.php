<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Segment\Wishlist\Controller\WishlistCommandController,
    Web\Segment\Wishlist\Controller\WishlistQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('wishlist', '/wishlist')
        ->controller([WishlistQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('wishlist_toggle', '/wishlist/toggle')
        ->controller([WishlistCommandController::class, 'toggle'])
        ->methods(['POST']);

    $routes->add('wishlist_destroy', '/wishlist/destroy')
        ->controller([WishlistCommandController::class, 'destroy'])
        ->methods(['POST']);
};
