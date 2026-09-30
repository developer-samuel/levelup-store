<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\Variant\AdminApiVariantQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api variants list
    $routes->add('api_admin_variants_list', '/api/admin/variants/list/{id}')
        ->controller([AdminApiVariantQueryController::class, 'list'])
        ->methods(['GET']);
};
