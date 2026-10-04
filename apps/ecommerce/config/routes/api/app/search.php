<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Search\SearchApiQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('search', '/search')
        ->controller([SearchApiQueryController::class, 'search'])
        ->methods(['GET']);
};
