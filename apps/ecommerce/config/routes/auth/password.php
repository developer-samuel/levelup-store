<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Auth\Controller\Command\ForgotPasswordCommandController,
    Auth\Controller\Command\ResetPasswordCommandController,
    Auth\Controller\Query\ForgotPasswordQueryController,
    Auth\Controller\Query\ResetPasswordQueryController
};

return function (RoutingConfigurator $routes) {
    // Routes for forgot password
    $routes->add('forgot_password', '/forgot-password')
        ->controller([ForgotPasswordQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('forgot_password_store', '/forgot-password/store')
        ->controller([ForgotPasswordCommandController::class, 'store'])
        ->methods(['POST']);

    // Routes for reset password
    $routes->add('reset_password', '/reset-password/{token}')
        ->controller([ResetPasswordQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('reset_password_store', '/reset-password/store')
        ->controller([ResetPasswordCommandController::class, 'store'])
        ->methods(['POST']);
};
