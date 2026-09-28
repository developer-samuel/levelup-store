<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Segment\Wishlist\Controller\WishlistCommandController,
    Segment\Wishlist\Controller\WishlistQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for wishlist page
    $routes->add('wishlist', '/wishlist')
        ->controller([WishlistQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for toggle wishlist data
    $routes->add('wishlist_toggle', '/wishlist/toggle')
        ->controller([WishlistCommandController::class, 'toggle'])
        ->methods(['POST']);

    // Route for destroy wishlist data
    $routes->add('wishlist_destroy', '/wishlist/destroy')
        ->controller([WishlistCommandController::class, 'destroy'])
        ->methods(['POST']);
};
