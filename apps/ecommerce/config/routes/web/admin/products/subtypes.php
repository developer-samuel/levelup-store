<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Product\Controller\Query\AdminProductSubtypeQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('products_subtypes_index', '/products/subtypes/{id}')
        ->controller([AdminProductSubtypeQueryController::class, 'index'])
        ->methods(['GET']);
};
