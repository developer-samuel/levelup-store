<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Segment\User\Controller\Command\ProfileCommandController,
    Segment\User\Controller\Query\ProfileQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for user profile page
    $routes->add('profile', '/profile')
        ->controller([ProfileQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for updating user profile
    $routes->add('profile_update', '/profile/update')
        ->controller([ProfileCommandController::class, 'update'])
        ->methods(['POST']);

    // Route for deleting user profile
    $routes->add('profile_destroy', '/profile/destroy')
        ->controller([ProfileCommandController::class, 'destroy'])
        ->methods(['POST']);
};
