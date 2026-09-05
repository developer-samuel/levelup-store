<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\AdminApiProductSubtypeQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api product subtypes list
    $routes->add('api_admin_products_subtypes_list', '/api/admin/products/subtypes/list/{id}')
        ->controller([AdminApiProductSubtypeQueryController::class, 'list'])
        ->methods(['GET']);
};
