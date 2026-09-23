<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes) {
    $routes->import(__DIR__.'/endpoints/assistant.php');
    $routes->import(__DIR__.'/endpoints/auth.php');
    $routes->import(__DIR__.'/endpoints/cookies.php');
    $routes->import(__DIR__.'/endpoints/search.php');
    $routes->import(__DIR__.'/endpoints/dev.php');
};
