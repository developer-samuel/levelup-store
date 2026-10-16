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

        $headers->set('Cross-Origin-Resource-Policy', 'same-origin');
        $headers->remove('X-Powered-By');
    }
}
