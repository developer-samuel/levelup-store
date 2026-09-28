<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Listener;

use Symfony\{
    Component\EventDispatcher\Attribute\AsEventListener,
    Component\HttpKernel\Event\RequestEvent,
    Component\HttpKernel\KernelEvents,
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\Security\Provider\SecurityProviderContract;

use App\Shared\{
    Constants\PathConstants,
    Responder\ErrorResponder
};

#[AsEventListener(
    event: KernelEvents::REQUEST,
    method: 'onKernelRequest',
)]
final readonly class AuthCheckerListener
{
    /**
     * @param SecurityProviderContract $securityProvider
     * @param ErrorResponder $errorResponder
    */
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private ErrorResponder $errorResponder,
    ) {}

    /**
     * @param RequestEvent $event
     *
     * @return void
    */
    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!$this->isPathProtected($path)) {
            return;
        }

        if (!$this->isUserAuthenticated()) {
            $this->handleUnauthorizedAccess($event);
        }
    }

    /**
     * @return string[]
    */
    private function protectedPaths(): array
    {
        return PathConstants::SECURITY_PATHS;
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isPathProtected(string $path): bool
    {
        foreach ($this->protectedPaths() as $protectedPath) {
            if ($path === $protectedPath || str_starts_with($path, $protectedPath . '/')) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
    */
    private function isUserAuthenticated(): bool
    {
        $user = $this->securityProvider->getCurrentUser();

        return $user instanceof User;
    }

    /**
     * @param RequestEvent $event
     *
     * @return void
    */
    private function handleUnauthorizedAccess(RequestEvent $event): void
    {
        $response = $this->errorResponder->renderUnauthorized();
        $event->setResponse($response);
    }
}
