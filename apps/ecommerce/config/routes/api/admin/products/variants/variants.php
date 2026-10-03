<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\Variant\AdminApiVariantQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_list', '/variants/list/{id}')
        ->controller([AdminApiVariantQueryController::class, 'list'])
        ->methods(['GET']);
};
