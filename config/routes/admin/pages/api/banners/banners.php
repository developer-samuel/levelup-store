<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Admin\Api\Banner\AdminApiBannerQueryController;

return function (RoutingConfigurator $routes) {
    // Route for admin api orders list
    $routes->add('api_admin_banners_list', '/api/admin/banners/list')
        ->controller([AdminApiBannerQueryController::class, 'list'])
        ->methods(['GET']);
};
