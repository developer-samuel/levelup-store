<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Segment\Review\Controller\Command\ReviewRatingCommandController;

return function (RoutingConfigurator $routes) {
    // Route for toggle review rating data
    $routes->add('reviews_ratings_toggle', '/reviews/ratings/toggle')
        ->controller([ReviewRatingCommandController::class, 'toggle'])
        ->methods(['POST']);
};
