<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Admin\Product\Controller\Command\Variant\AdminVariantDescriptionCommandController,
    Web\Admin\Product\Controller\Query\Variant\AdminVariantDescriptionQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('variants_descriptions_index', '/variants/descriptions/{id}')
        ->controller([AdminVariantDescriptionQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('variants_descriptions_create', '/variants/descriptions/create/{id}')
        ->controller([AdminVariantDescriptionQueryController::class, 'create'])
        ->methods(['GET']);

    $routes->add('variants_descriptions_store', '/variants/descriptions/store')
        ->controller([AdminVariantDescriptionCommandController::class, 'store'])
        ->methods(['POST']);

    $routes->add(
        'variants_descriptions_edit',
        '/variants/descriptions/edit/{variantId}/{descriptionId}'
    )
    ->controller([AdminVariantDescriptionQueryController::class, 'edit'])
    ->methods(['GET']);

    $routes->add('variants_descriptions_update', '/variants/descriptions/update')
        ->controller([AdminVariantDescriptionCommandController::class, 'update'])
        ->methods(['POST']);

    $routes->add('variants_descriptions_destroy', '/variants/descriptions/destroy')
        ->controller([AdminVariantDescriptionCommandController::class, 'destroy'])
        ->methods(['POST']);
};
