<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Auth\Controller\Query\VerificationQueryController,
    Auth\Controller\Command\VerificationCommandController
};

return function (RoutingConfigurator $routes) {
    // Route for must verify page
    $routes->add('must_verify', '/must-verify')
        ->controller([VerificationQueryController::class, 'show'])
        ->methods(['GET']);

    // Route for verification send mail
    $routes->add('verification_store', '/verification/store')
        ->controller([VerificationCommandController::class, 'store'])
        ->methods(['POST']);

    // Route for updating verificatiíon status
    $routes->add('verification_update', '/verification/update')
        ->controller([VerificationCommandController::class, 'update'])
        ->methods(['GET']);
};
