<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Brand\AdminApiBrandQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('brands_list', '/brands/list')
        ->controller([AdminApiBrandQueryController::class, 'list'])
        ->methods(['GET']);
};
