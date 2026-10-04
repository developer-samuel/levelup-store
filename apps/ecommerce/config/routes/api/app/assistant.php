<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Api\Assistant\AssistantApiQueryController;

return function (RoutingConfigurator $routes): void {
    $routes->add('assistant_session', '/assistant/session')
        ->controller([AssistantApiQueryController::class, 'session'])
        ->methods(['GET']);
};
