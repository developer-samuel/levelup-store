<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Segment\Product\Controller\ProductQueryController;

return function (RoutingConfigurator $routes) {
    // Route for displaying products based on category and type
    $routes->add('products_index', '/products/{category}/{type}')
        ->controller([ProductQueryController::class, 'index'])
        ->methods(['GET'])
        ->defaults(['category' => null, 'type' => null]);

    // Route for displaying a single product by URL
    $routes->add('product_show', '/product/show/{url}')
        ->controller([ProductQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for displaying discounts based on category and type
    $routes->add('discounts', '/discounts/{category}/{type}')
        ->controller([ProductQueryController::class, 'index'])
        ->methods(['GET'])
        ->defaults(['category' => null, 'type' => null]);
};
