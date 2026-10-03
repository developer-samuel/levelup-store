<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\Variant\AdminApiVariantEanQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_eans_list', '/variants/eans/list/{id}')
        ->controller([AdminApiVariantEanQueryController::class, 'list'])
        ->methods(['GET']);
};
