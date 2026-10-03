<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Segment\User\Controller\Command\ProfileCommandController,
    Web\Segment\User\Controller\Query\ProfileQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('profile', '/profile')
        ->controller([ProfileQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('profile_update', '/profile/update')
        ->controller([ProfileCommandController::class, 'update'])
        ->methods(['POST']);

    $routes->add('profile_destroy', '/profile/destroy')
        ->controller([ProfileCommandController::class, 'destroy'])
        ->methods(['POST']);
};
