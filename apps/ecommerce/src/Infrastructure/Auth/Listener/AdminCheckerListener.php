<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Listener;

use Symfony\{
    Component\EventDispatcher\Attribute\AsEventListener,
    Component\HttpFoundation\Response,
    Component\HttpKernel\Event\RequestEvent,
    Component\HttpKernel\KernelEvents
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\User\Service\Query\UserQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Shared\Responder\ErrorResponder;

#[AsEventListener(
    event: KernelEvents::REQUEST,
    method: 'onKernelRequest',
)]
final readonly class AdminCheckerListener
{
    public function __construct(
        private SecurityProviderContract $securityProvider,
        private UserQueryContract $userQuery,
        private ErrorResponder $errorResponder,
        private AppLoggerContract $logger,
    ) {}

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!$this->isAdminPath($path)) {
            return;
        }

        $user = $this->securityProvider->getCurrentUser();
        $response = $this->getAdminAccessResponse($user);

        if ($response !== null) {
            $this->logAccessBlocked($user, $path);
            $event->setResponse($response);
        }
    }

    private function isAdminPath(string $path): bool
    {
        return $path === '/admin' || str_starts_with($path, '/admin/');
    }

    private function getAdminAccessResponse(?User $user): ?Response
    {
        if (!$this->isValidAdminUser($user)) {
            return $this->errorResponder->renderNotFound();
        }

        return null;
    }

    private function isValidAdminUser(?User $user): bool
    {
        if (!$user instanceof User) {
            return false;
        }

        return $this->userQuery->isAdmin($user);
    }

    private function logAccessBlocked(?User $user, string $path): void
    {
        $this->logger->warning(
            'Access to the admin page blocked',
            $user,
            ['path' => $path],
        );
    }
}
