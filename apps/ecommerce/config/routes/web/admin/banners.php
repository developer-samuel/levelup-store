<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Admin\Banner\AdminBannerQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('banners_index', '/banners')
        ->controller([AdminBannerQueryController::class, 'index'])
        ->methods(['GET']);
};
