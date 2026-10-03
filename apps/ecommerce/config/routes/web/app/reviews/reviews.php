<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Segment\Review\Controller\Command\ReviewCommandController,
    Web\Segment\Review\Controller\Query\ReviewQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('reviews_index', '/reviews/{url}')
        ->controller([ReviewQueryController::class, 'index'])
        ->methods(['GET']);

    $routes->add('reviews_store', '/reviews/store')
        ->controller([ReviewCommandController::class, 'store'])
        ->methods(['POST']);

    $routes->add('reviews_destroy', '/reviews/destroy')
        ->controller([ReviewCommandController::class, 'destroy'])
        ->methods(['POST']);
};
