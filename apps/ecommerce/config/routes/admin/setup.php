<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    // Route for admin home page
    $routes->import(__DIR__.'/pages/home/home.php');

    // Admin routes
    $routes->import(__DIR__.'/pages/dashboard/dashboard.php');
    $routes->import(__DIR__.'/pages/banners/banners.php');
    $routes->import(__DIR__.'/pages/brands/brands.php');
    $routes->import(__DIR__.'/pages/users/users.php');

    // Admin orders routes
    $routes->import(__DIR__.'/pages/orders/orders.php');
    $routes->import(__DIR__.'/pages/orders/history.php');

    // Admin products and variants routes
    $routes->import(__DIR__.'/pages/products/products.php');
    $routes->import(__DIR__.'/pages/products/subtypes.php');
    $routes->import(__DIR__.'/pages/products/variants/variants.php');
    $routes->import(__DIR__.'/pages/products/variants/eans.php');
    $routes->import(__DIR__.'/pages/products/variants/images.php');
    $routes->import(__DIR__.'/pages/products/variants/descriptions.php');

    // Admin api routes
    $routes->import(__DIR__.'/pages/api/banners/banners.php');
    $routes->import(__DIR__.'/pages/api/brands/brands.php');
    $routes->import(__DIR__.'/pages/api/users/users.php');

    // Admin api orders routes
    $routes->import(__DIR__.'/pages/api/orders/orders.php');
    $routes->import(__DIR__.'/pages/api/orders/history.php');

    // Admin api products and variantsroutes
    $routes->import(__DIR__.'/pages/api/products/products.php');
    $routes->import(__DIR__.'/pages/api/products/subtypes.php');
    $routes->import(__DIR__.'/pages/api/products/variants/variants.php');
    $routes->import(__DIR__.'/pages/api/products/variants/eans.php');
    $routes->import(__DIR__.'/pages/api/products/variants/images.php');
    $routes->import(__DIR__.'/pages/api/products/variants/descriptions.php');
};