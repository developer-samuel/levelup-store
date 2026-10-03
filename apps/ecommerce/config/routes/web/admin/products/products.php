<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Product\Controller\Query\AdminProductQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('products_index', '/products')
        ->controller([AdminProductQueryController::class, 'index'])
        ->methods(['GET']);
};
