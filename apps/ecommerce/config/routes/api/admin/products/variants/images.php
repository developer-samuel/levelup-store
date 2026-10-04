<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\Variant\AdminApiVariantImageQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('variants_images_list', '/variants/images/list/{id}')
        ->controller([AdminApiVariantImageQueryController::class, 'list'])
        ->methods(['GET']);
};
