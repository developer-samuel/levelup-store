<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    // Auth
    $routes->import(__DIR__.'/endpoints/auth.php');

    // Assistant
    $routes->import(__DIR__.'/endpoints/assistant.php');

    // Features
    $routes->import(__DIR__.'/endpoints/search.php');
    $routes->import(__DIR__.'/endpoints/cookies.php');

    // Dev - local/dev/test only (includes Swagger UI at /api/dev/docs)
    if (in_array($routes->env(), ['local', 'dev', 'test'], true)) {
        $routes->import(__DIR__.'/endpoints/dev.php');
    }
};
