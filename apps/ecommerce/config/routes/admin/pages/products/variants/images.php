<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Product\Controller\Query\Variant\AdminVariantImageQueryController;

return function (RoutingConfigurator $routes) { 
    // Route for admin variant images page
    $routes->add('admin_variants_images_index', '/admin/variants/images/{id}')
        ->controller([AdminVariantImageQueryController::class, 'index'])
        ->methods(['GET']);
};
