<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

use App\Presentation\Assistant\AssistantApiQueryController;

return function (RoutingConfigurator $routes): void {
    $routes->add('api_assistant_session', '/api/assistant/session')
        ->controller([AssistantApiQueryController::class, 'session'])
        ->methods(['GET']);
};
