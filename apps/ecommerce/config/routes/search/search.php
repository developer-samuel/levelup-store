<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Search\Controller\Query\SearchQueryController;

return function (RoutingConfigurator $routes) {
    // Route for search page
    $routes->add('search_find', '/search/find')
        ->controller([SearchQueryController::class, 'index'])
        ->methods(['GET']);
};
