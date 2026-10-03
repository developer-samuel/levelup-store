<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\AdminApiProductSubtypeQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('products_subtypes_list', '/products/subtypes/list/{id}')
        ->controller([AdminApiProductSubtypeQueryController::class, 'list'])
        ->methods(['GET']);
};
