<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Cookie\Controller\Command\CookieApiCommandController;

return function (RoutingConfigurator $routes) {
    // Route for storing cookie preferences
    $routes->add('api_cookies_store', '/api/cookies/store')
        ->controller([CookieApiCommandController::class, 'store'])
        ->methods(['POST']);
};
