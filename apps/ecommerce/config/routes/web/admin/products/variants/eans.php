<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Admin\Product\Controller\Command\Variant\AdminVariantEanCommandController,
    Web\Admin\Product\Controller\Query\Variant\AdminVariantEanQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('variants_eans_index', '/variants/eans/{id}')
        ->controller([AdminVariantEanQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('variants_eans_create', '/variants/eans/create/{id}')
        ->controller([AdminVariantEanQueryController::class, 'create'])
        ->methods(['GET']);

    $routes->add('variants_eans_store', '/variants/eans/store')
        ->controller([AdminVariantEanCommandController::class, 'store'])
        ->methods(['POST']);

    $routes->add(
        'variants_eans_edit',
        '/variants/eans/edit/{variantId}/{eanId}'
    )
    ->controller([AdminVariantEanQueryController::class, 'edit'])
    ->methods(['GET']);

    $routes->add('variants_eans_update', '/variants/eans/update')
        ->controller([AdminVariantEanCommandController::class, 'update'])
        ->methods(['POST']);

    $routes->add('variants_eans_destroy', '/variants/eans/destroy')
        ->controller([AdminVariantEanCommandController::class, 'destroy'])
        ->methods(['POST']);
};
