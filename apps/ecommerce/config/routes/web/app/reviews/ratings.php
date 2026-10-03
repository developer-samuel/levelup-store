<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Web\Segment\Review\Controller\Command\ReviewRatingCommandController;

return function (RoutingConfigurator $routes) {
    $routes->add('reviews_ratings_toggle', '/reviews/ratings/toggle')
        ->controller([ReviewRatingCommandController::class, 'toggle'])
        ->methods(['POST']);
};
