<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Segment\User\Controller\Command\ChangePasswordCommandController,
    Segment\User\Controller\Query\ChangePasswordQueryController
};

return function (RoutingConfigurator $routes) {
    // Route for change password page
    $routes->add('change_password', '/change-password')
        ->controller([ChangePasswordQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for updating user password
    $routes->add('change_password_update', '/change-password/update')
        ->controller([ChangePasswordCommandController::class, 'update'])
        ->methods(['POST']);
};
