<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    // API routes
    $routes->import(__DIR__.'/api/setup.php');

    // Admin routes
    $routes->import(__DIR__.'/admin/setup.php');

    // Cart routes
    $routes->import(__DIR__.'/cart/cart.php');

    // Authentication routes
    $routes->import(__DIR__.'/auth/login.php');
    $routes->import(__DIR__.'/auth/password.php');
    $routes->import(__DIR__.'/auth/signup.php');
    $routes->import(__DIR__.'/auth/verification.php');

    // Home routes
    $routes->import(__DIR__.'/home/home.php');

    // Search routes
    $routes->import(__DIR__.'/search/search.php');

    // Product routes
    $routes->import(__DIR__.'/products/products.php');

    // Wishlist routes
    $routes->import(__DIR__.'/wishlist/wishlist.php');

    // Order routes
    $routes->import(__DIR__.'/orders/orders.php');
    $routes->import(__DIR__.'/orders/status.php');

    // Invoice routes
    $routes->import(__DIR__.'/invoice/orders.php');

    // User routes
    $routes->import(__DIR__.'/user/password.php');
    $routes->import(__DIR__.'/user/profile.php');

    // Review routes
    $routes->import(__DIR__.'/reviews/reviews.php');
    $routes->import(__DIR__.'/reviews/ratings.php');
};
