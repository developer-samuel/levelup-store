<?php

declare(strict_types=1);

namespace App\Infrastructure\Auth\Listener;

use Symfony\{
    Component\EventDispatcher\Attribute\AsEventListener,
    Component\HttpFoundation\RedirectResponse,
    Component\HttpKernel\Event\ControllerEvent,
    Component\HttpKernel\KernelEvents,
    Component\Routing\Generator\UrlGeneratorInterface,
    Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface
};

use App\Core\Domain\Segment\User\Entity\User;

use App\Shared\Constants\PathConstants;

#[AsEventListener(
    event: KernelEvents::CONTROLLER,
    method: 'onKernelController',
)]
final readonly class GuestCheckerListener
{
    /**
     * @param TokenStorageInterface $tokenStorage
     * @param UrlGeneratorInterface $urlGenerator
    */
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private UrlGeneratorInterface $urlGenerator,
    ) {}

    /**
     * @param ControllerEvent $event
     *
     * @return void
    */
    public function onKernelController(ControllerEvent $event): void
    {
        $request = $event->getRequest();
        $path = $request->getPathInfo();

        if (!$this->isGuestPath($path)) {
            return;
        }

        $user = $this->tokenStorage->getToken()?->getUser();

        if ($user instanceof User) {
            $homeUrl = $this->urlGenerator->generate('home');
            $event->setController(fn () => new RedirectResponse($homeUrl));
        }
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isGuestPath(string $path): bool
    {
        return in_array($path, PathConstants::GUEST_PATHS, true);
    }
}
