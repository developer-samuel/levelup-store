<?php

declare(strict_types=1);

use Symfony\{
    Component\Dotenv\Dotenv,
    Component\ErrorHandler\Debug,
    Component\HttpFoundation\Request
};

use Twig\{
    Environment,
    Loader\FilesystemLoader
};

use App\Kernel;

use App\Shared\Renderer\ErrorRenderer;

// Load the Composer autoloader which provides access to all the dependencies
require __DIR__ . '/vendor/autoload.php';

// Load environment variables from the .env file
(new Dotenv())->bootEnv(__DIR__ . '/.env');

// If debug mode is enabled (APP_DEBUG), set file permissions mask and enable debugging
if ((bool) $_SERVER['APP_DEBUG']) {
    umask(0000); // Set file creation permissions to allow full access
    Debug::enable(); // Enable debug mode for detailed error reporting
}

$requestUri = $_SERVER['REQUEST_URI'] ?? '';
$publicDir = realpath(__DIR__ . '/public');
$requestedPath = realpath(__DIR__ . '/public' . parse_url($requestUri, PHP_URL_PATH));

// If not running on the built-in PHP server (cli-server), and the requested file exists, return a 404 error
if (php_sapi_name() !== 'cli-server' && $publicDir !== false && $requestedPath !== false && str_starts_with($requestedPath, $publicDir) && !is_dir($requestedPath)) {
    // Set HTTP status to 404 and render the 404 error page using the Twig template engine
    http_response_code(404);
    $loader = new FilesystemLoader(__DIR__ . '/templates');
    $twig = new Environment($loader);

    // Render 404 error page using ErrorRenderer
    $errorRenderer = new ErrorRenderer($twig);

    $message = 'The requested page was not found.';

    echo $errorRenderer->renderNotFound($message);
    exit;
}

// Create the request object from global server variables
$request = Request::createFromGlobals();

// Initialize the application kernel with the current environment and debug mode
$kernel = new Kernel($_SERVER['APP_ENV'], (bool) $_SERVER['APP_DEBUG']);

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);
