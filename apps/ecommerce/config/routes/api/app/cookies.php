<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Cookie\CookieApiCommandController;

return function (RoutingConfigurator $routes) {
    $routes->add('cookies_store', '/cookies/store')
        ->controller([CookieApiCommandController::class, 'store'])
        ->methods(['POST']);
};
