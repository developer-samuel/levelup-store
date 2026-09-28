<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Admin\Feature\Product\Controller\Command\Variant\AdminVariantEanCommandController,
    Admin\Feature\Product\Controller\Query\Variant\AdminVariantEanQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for admin variant eans page
    $routes->add('admin_variants_eans_index', '/admin/variants/eans/{id}')
        ->controller([AdminVariantEanQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for admin variant eans create page
    $routes->add('admin_variants_eans_create', '/admin/variants/eans/create/{id}')
        ->controller([AdminVariantEanQueryController::class, 'create'])
        ->methods(['GET']);

    // Route for admin variant eans store
    $routes->add('admin_variants_eans_store', '/admin/variants/eans/store')
        ->controller([AdminVariantEanCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for admin variant eans edit page
    $routes->add(
        'admin_variants_eans_edit',
        '/admin/variants/eans/edit/{variantId}/{eanId}'
    )
    ->controller([AdminVariantEanQueryController::class, 'edit'])
    ->methods(['GET']);

    // Route for admin variant eans update
    $routes->add('admin_variants_eans_update', '/admin/variants/eans/update')
        ->controller([AdminVariantEanCommandController::class, 'update'])
        ->methods(['POST']);

    // Route for admin variant eans destroy
    $routes->add('admin_variants_eans_destroy', '/admin/variants/eans/destroy')
        ->controller([AdminVariantEanCommandController::class, 'destroy'])
        ->methods(['POST']);
};
