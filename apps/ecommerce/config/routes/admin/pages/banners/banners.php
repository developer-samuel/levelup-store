<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Feature\Banner\AdminBannerQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin banners page
    $routes->add('admin_banners_index', '/admin/banners')
        ->controller([AdminBannerQueryController::class, 'index'])
        ->methods(['GET']);
};
