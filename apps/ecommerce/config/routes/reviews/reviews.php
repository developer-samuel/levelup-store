<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Segment\Review\Controller\Command\ReviewCommandController,
    Segment\Review\Controller\Query\ReviewQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for reviews page
    $routes->add('reviews_index', '/reviews/{url}')
        ->controller([ReviewQueryController::class, 'index'])
        ->methods(['GET']);

    // Route for review store data
    $routes->add('reviews_store', '/reviews/store')
        ->controller([ReviewCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for destroy review data
    $routes->add('reviews_destroy', '/reviews/destroy')
        ->controller([ReviewCommandController::class, 'destroy'])
        ->methods(['POST']);
};
