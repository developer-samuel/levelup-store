<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Search\SearchQueryController;

return function (RoutingConfigurator $routes) {
    $routes->add('search_find', '/search/find')
        ->controller([SearchQueryController::class, 'index'])
        ->methods(['GET']);
};
