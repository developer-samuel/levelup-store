<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Product\Controller\Query\AdminApiProductQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('products_list', '/products/list')
        ->controller([AdminApiProductQueryController::class, 'list'])
        ->methods(['GET']);
};
