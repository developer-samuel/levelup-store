<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Auth\Controller\Command\ForgotPasswordCommandController,
    Web\Auth\Controller\Command\ResetPasswordCommandController,
    Web\Auth\Controller\Query\ForgotPasswordQueryController,
    Web\Auth\Controller\Query\ResetPasswordQueryController
};

return function (RoutingConfigurator $routes) {
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
