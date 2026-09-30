<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\Variant\AdminApiVariantEanQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api variant eans list
    $routes->add('api_admin_variants_eans_list', '/api/admin/variants/eans/list/{id}')
        ->controller([AdminApiVariantEanQueryController::class, 'list'])
        ->methods(['GET']);
};
