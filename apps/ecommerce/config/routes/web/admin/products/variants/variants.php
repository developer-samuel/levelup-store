<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Product\Controller\Query\Variant\AdminVariantQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_index', '/variants/{id}')
        ->controller([AdminVariantQueryController::class, 'index'])
        ->methods(['GET']);
};
