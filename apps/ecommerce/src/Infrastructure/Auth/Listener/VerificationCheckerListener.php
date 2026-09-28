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
final readonly class VerificationCheckerListener
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

        $user = $this->tokenStorage->getToken()?->getUser();
        if (!$user instanceof User) {
            return;
        }

        $mustVerifyPath = $this->getMustVerifyPath();

        if ($user->getEmailVerifiedAt() === null) {
            if (!$this->isVerificationPaths($path)) {
                $event->setController(fn () => new RedirectResponse($mustVerifyPath));
            }

            return;
        }

        if ($path === $mustVerifyPath) {
            $event->setController(fn () => new RedirectResponse(
                $this->urlGenerator->generate('home'),
            ));
        }
    }

    /**
     * @param string $path
     *
     * @return bool
    */
    private function isVerificationPaths(string $path): bool
    {
        if ($path === PathConstants::VERIFY_BASE_PATH) {
            return true;
        }

        if ($path === $this->getMustVerifyPath()) {
            return true;
        }

        if ($path === $this->getVerificationStorePath()) {
            return true;
        }

        return $path === $this->getVerificationUpdatePath();
    }

    /**
     * @return string
    */
    private function getMustVerifyPath(): string
    {
        return $this->urlGenerator->generate('must_verify', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @return string
    */
    private function getVerificationStorePath(): string
    {
        return $this->urlGenerator->generate('verification_store', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }

    /**
     * @return string
    */
    private function getVerificationUpdatePath(): string
    {
        return $this->urlGenerator->generate('verification_update', [], UrlGeneratorInterface::ABSOLUTE_PATH);
    }
}
