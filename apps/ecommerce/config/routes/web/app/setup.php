<?php

declare(strict_types=1);

use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

function importGroup(RoutingConfigurator $routes, string $dir, array $files): void
{
    foreach ($files as $file) {
        $routes->import($dir.$file);
    }
}

return function (RoutingConfigurator $routes): void {
    $routes->import(__DIR__ . '/cart.php');
    $routes->import(__DIR__ . '/home.php');
    $routes->import(__DIR__ . '/search.php');
    $routes->import(__DIR__ . '/products.php');
    $routes->import(__DIR__ . '/wishlist.php');

    importGroup($routes, __DIR__ . '/auth', [
        '/login.php',
        '/password.php',
        '/signup.php',
        '/verification.php',
    ]);

    importGroup($routes, __DIR__ . '/orders', [
        '/orders.php',
        '/status.php',
        '/invoice.php',
    ]);

    importGroup($routes, __DIR__ . '/user', [
        '/password.php',
        '/profile.php',
    ]);

    importGroup($routes, __DIR__ . '/reviews', [
        '/reviews.php',
        '/ratings.php',
    ]);
};
