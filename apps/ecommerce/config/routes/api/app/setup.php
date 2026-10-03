<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/auth.php');
    $routes->import(__DIR__ . '/assistant.php');
    $routes->import(__DIR__ . '/search.php');
    $routes->import(__DIR__ . '/cookies.php');
};
