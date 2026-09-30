<?php

declare(strict_types=1);

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (str_starts_with($uri, '/dist/ecommerce/')) {
    $file = dirname(__DIR__, 2) . '/dist/ecommerce/' . substr($uri, strlen('/dist/ecommerce/'));

    if (is_file($file)) {
        $ext = pathinfo($file, PATHINFO_EXTENSION);

        $mime = match ($ext) {
            'css'  => 'text/css',
            'js'   => 'application/javascript',
            'map'  => 'application/json',
            'woff' => 'font/woff',
            'woff2'=> 'font/woff2',
            'ttf'  => 'font/ttf',
            'svg'  => 'image/svg+xml',
            'png'  => 'image/png',
            'jpg', 'jpeg' => 'image/jpeg',
            'ico'  => 'image/x-icon',
            'json' => 'application/json',
            default => 'application/octet-stream',
        };

        header('Content-Type: ' . $mime);

        readfile($file);

        return true;
    }

    http_response_code(404);

    return true;
}

return false;
