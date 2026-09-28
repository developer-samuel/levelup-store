<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\Variant\AdminApiVariantDescriptionQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api variant descriptions list
    $routes->add('api_admin_variants_descriptions_list', '/api/admin/variants/descriptions/list/{id}')
        ->controller([AdminApiVariantDescriptionQueryController::class, 'list'])
        ->methods(['GET']);
};
