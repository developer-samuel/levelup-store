<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Search\Controller\Query\SearchApiQueryController;

return function (RoutingConfigurator $routes) {
    // Route for api finding products
    $routes->add('api_search', '/api/search')
        ->controller([SearchApiQueryController::class, 'search'])
        ->methods(['GET']);
};
