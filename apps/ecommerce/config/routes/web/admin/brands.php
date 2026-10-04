<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Admin\Brand\Controller\AdminBrandCommandController,
    Web\Admin\Brand\Controller\AdminBrandQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('brands_index', '/brands')
        ->controller([AdminBrandQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('brands_create', '/brands/create')
        ->controller([AdminBrandQueryController::class, 'create'])
        ->methods(['GET']);

    $routes->add('brand_store', '/brands/store')
        ->controller([AdminBrandCommandController::class, 'store'])
        ->methods(['POST']);

    $routes->add('brands_edit', '/brands/edit/{id}')
        ->controller([AdminBrandQueryController::class, 'edit'])
        ->methods(['GET']);

    $routes->add('brand_update', '/brands/update')
        ->controller([AdminBrandCommandController::class, 'update'])
        ->methods(['POST']);

    $routes->add('brand_destroy', '/brands/destroy')
        ->controller([AdminBrandCommandController::class, 'destroy'])
        ->methods(['POST']);
};
