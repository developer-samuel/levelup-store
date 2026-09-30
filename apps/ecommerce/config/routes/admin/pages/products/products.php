<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Product\Controller\Query\AdminProductQueryController;

return function (RoutingConfigurator $routes) { 
    // Route for admin products page
    $routes->add('admin_products_index', '/admin/products')
        ->controller([AdminProductQueryController::class, 'index'])
        ->methods(['GET']);
};
