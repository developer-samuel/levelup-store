<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Product\Controller\Query\Variant\AdminApiVariantImageQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api variant images list
    $routes->add('api_admin_variants_images_list', '/api/admin/variants/images/list/{id}')
        ->controller([AdminApiVariantImageQueryController::class, 'list'])
        ->methods(['GET']);
};
