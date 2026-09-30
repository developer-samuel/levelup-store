<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Product\Controller\Query\Variant\AdminVariantQueryController;

return function (RoutingConfigurator $routes) { 
    // Route for admin variants page
    $routes->add('admin_variants_index', '/admin/variants/{id}')
        ->controller([AdminVariantQueryController::class, 'index'])
        ->methods(['GET']);
};
