<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\{
    Web\Auth\Controller\Query\VerificationQueryController,
    Web\Auth\Controller\Command\VerificationCommandController
};

return function (RoutingConfigurator $routes) {
    $routes->add('must_verify', '/must-verify')
        ->controller([VerificationQueryController::class, 'show'])
        ->methods(['GET']);

    $routes->add('verification_store', '/verification/store')
        ->controller([VerificationCommandController::class, 'store'])
        ->methods(['POST']);

    $routes->add('verification_update', '/verification/update')
        ->controller([VerificationCommandController::class, 'update'])
        ->methods(['GET']);
};
