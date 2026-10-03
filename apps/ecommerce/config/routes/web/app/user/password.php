<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Segment\User\Controller\Command\ChangePasswordCommandController,
    Web\Segment\User\Controller\Query\ChangePasswordQueryController
};

return function (RoutingConfigurator $routes) {
    $routes->add('change_password', '/change-password')
        ->controller([ChangePasswordQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('change_password_update', '/change-password/update')
        ->controller([ChangePasswordCommandController::class, 'update'])
        ->methods(['POST']);
};
