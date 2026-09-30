<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Admin\Feature\Brand\Controller\AdminBrandCommandController,
    Admin\Feature\Brand\Controller\AdminBrandQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for admin brands page
    $routes->add('admin_brands_index', '/admin/brands')
        ->controller([AdminBrandQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for admin brand create page
    $routes->add('admin_brands_create', '/admin/brands/create')
        ->controller([AdminBrandQueryController::class, 'create'])
        ->methods(['GET']);

    // Route for admin brand store
    $routes->add('admin_brand_store', '/admin/brands/store')
        ->controller([AdminBrandCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for admin brand edit page
    $routes->add('admin_brands_edit', '/admin/brands/edit/{id}')
        ->controller([AdminBrandQueryController::class, 'edit'])
        ->methods(['GET']);

    // Route for admin brand update
    $routes->add('admin_brand_update', '/admin/brands/update')
        ->controller([AdminBrandCommandController::class, 'update'])
        ->methods(['POST']);

    // Route for admin brand destroy
    $routes->add('admin_brand_destroy', '/admin/brands/destroy')
        ->controller([AdminBrandCommandController::class, 'destroy'])
        ->methods(['POST']);
};
