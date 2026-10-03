<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\Variant\AdminApiVariantDescriptionQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_descriptions_list', '/variants/descriptions/list/{id}')
        ->controller([AdminApiVariantDescriptionQueryController::class, 'list'])
        ->methods(['GET']);
};
