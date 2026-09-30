<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Admin\Feature\Product\Controller\Command\Variant\AdminVariantDescriptionCommandController,
    Admin\Feature\Product\Controller\Query\Variant\AdminVariantDescriptionQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for admin variant descriptions page
    $routes->add('admin_variants_descriptions_index', '/admin/variants/descriptions/{id}')
        ->controller([AdminVariantDescriptionQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for admin variant descriptions create page
    $routes->add('admin_variants_descriptions_create', '/admin/variants/descriptions/create/{id}')
        ->controller([AdminVariantDescriptionQueryController::class, 'create'])
        ->methods(['GET']);

    // Route for admin variant descriptions store
    $routes->add('admin_variants_descriptions_store', '/admin/variants/descriptions/store')
        ->controller([AdminVariantDescriptionCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for admin variant descriptions edit page
    $routes->add(
        'admin_variants_descriptions_edit',
        '/admin/variants/descriptions/edit/{variantId}/{descriptionId}'
    )
    ->controller([AdminVariantDescriptionQueryController::class, 'edit'])
    ->methods(['GET']);

    // Route for admin variant descriptions update
    $routes->add('admin_variants_descriptions_update', '/admin/variants/descriptions/update')
        ->controller([AdminVariantDescriptionCommandController::class, 'update'])
        ->methods(['POST']);

    // Route for admin variant descriptions destroy
    $routes->add('admin_variants_descriptions_destroy', '/admin/variants/descriptions/destroy')
        ->controller([AdminVariantDescriptionCommandController::class, 'destroy'])
        ->methods(['POST']);
};
