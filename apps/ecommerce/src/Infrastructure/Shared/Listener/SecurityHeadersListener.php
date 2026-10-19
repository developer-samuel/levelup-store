<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Listener;

use Symfony\{
    Component\EventDispatcher\Attribute\AsEventListener,
    Component\HttpKernel\Event\ResponseEvent,
    Component\HttpKernel\KernelEvents,
};

#[AsEventListener(event: KernelEvents::RESPONSE)]
final class SecurityHeadersListener
{
    public function __invoke(ResponseEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $response = $event->getResponse();
        $headers = $response->headers;

        $headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        $headers->set('Cross-Origin-Embedder-Policy', 'require-corp');
        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $headers->set('X-Content-Type-Options', 'nosniff');
        $headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');
        $headers->set('Content-Security-Policy', "default-src 'self'; script-src 'self' 'unsafe-inline' https://js.stripe.com https://challenges.cloudflare.com; style-src 'self' 'unsafe-inline'; font-src 'self'; img-src 'self' data: https:; connect-src 'self' https://api.stripe.com; frame-src https://js.stripe.com https://challenges.cloudflare.com; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
        $headers->remove('X-Powered-By');
    }
}
