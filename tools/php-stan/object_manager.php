<?php

declare(strict_types=1);

use App\Kernel;
use Symfony\Component\Dotenv\Dotenv;

$root = dirname(__DIR__, 2);

require_once $root . '/vendor/autoload.php';

(new Dotenv())->bootEnv($root . '/.env');

$kernel = new Kernel($_SERVER['APP_ENV'] ?? 'test', (bool) ($_SERVER['APP_DEBUG'] ?? false));
$kernel->boot();

return $kernel->getContainer()->get('doctrine')->getManager();
