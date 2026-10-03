<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Segment\Product\Controller\ProductQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('products_index', '/products/{category}/{type}')
        ->controller([ProductQueryController::class, 'index'])
        ->methods(['GET'])
        ->defaults(['category' => null, 'type' => null]);

    $routes->add('product_show', '/product/show/{url}')
        ->controller([ProductQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('discounts', '/discounts/{category}/{type}')
        ->controller([ProductQueryController::class, 'index'])
        ->methods(['GET'])
        ->defaults(['category' => null, 'type' => null]);
};
