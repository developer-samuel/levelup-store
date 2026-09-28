<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Product\Controller\Query\AdminProductSubtypeQueryController;

return function (RoutingConfigurator $routes) { 
    // Route for admin product subtypes page
    $routes->add('admin_products_subtypes_index', '/admin/products/subtypes/{id}')
        ->controller([AdminProductSubtypeQueryController::class, 'index'])
        ->methods(['GET']);
};
