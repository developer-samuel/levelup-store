<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Admin\Banner\AdminApiBannerQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('banners_list', '/banners/list')
        ->controller([AdminApiBannerQueryController::class, 'list'])
        ->methods(['GET']);
};
