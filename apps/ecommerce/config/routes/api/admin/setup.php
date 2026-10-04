<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

const ADMIN_API_ROUTE_FILES = [
    '/banners.php',
    '/brands.php',
    '/users.php',

    // Orders
    '/orders/orders.php',
    '/orders/history.php',
    // Products

    '/products/products.php',
    '/products/subtypes.php',
    '/products/variants/variants.php',
    '/products/variants/eans.php',
    '/products/variants/images.php',
    '/products/variants/descriptions.php',
];

// All routes imported here inherit prefix '/admin' and name prefix 'admin_'.
// Applied on top of '/api' and 'api_' from api.php.
return function (RoutingConfigurator $routes): void {
    foreach (ADMIN_API_ROUTE_FILES as $file) {
        $routes->import(__DIR__ . $file)->prefix('/admin')->namePrefix('admin_');
    }
};
