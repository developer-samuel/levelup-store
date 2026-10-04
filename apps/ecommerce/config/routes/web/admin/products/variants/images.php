<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Product\Controller\Query\Variant\AdminVariantImageQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_images_index', '/variants/images/{id}')
        ->controller([AdminVariantImageQueryController::class, 'index'])
        ->methods(['GET']);
};
