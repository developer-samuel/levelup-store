<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Ports\{
    Cache\Service\Query\CoreCacheQueryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class CoreSubscriber implements EventSubscriberInterface
{
    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
     * @param CoreCacheQueryContract $coreCacheQuery
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private CoreCacheQueryContract $coreCacheQuery,
    ) {}

    /**
     * @return array<class-string, string>
    */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }

    /**
     * @param ControllerEvent $event
     *
     * @return void
    */
    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        $path = $event->getRequest()->getPathInfo();

        try {
            $vars = $this->coreCacheQuery->getVars($path);

            $this->addGlobalVariablesToTwig(
                $vars->path,
                $vars->guestPaths,
                $vars->adminPaths,
                $vars->isAdminPath,
                $vars->showHeader,
                $vars->showFooter,
                $vars->showAssistant,
            );
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'CoreSubscriber error while fetching values',
                $throwable,
            );

            $this->addGlobalVariablesToTwig('', [], [], false, false, false, false);
        }
    }

    /**
     * @param string $path
     * @param string[] $guestPaths
     * @param string[] $adminPaths
     * @param bool $isAdminPath
     * @param bool $showHeader
     * @param bool $showFooter
     *
     * @return void
    */
    private function addGlobalVariablesToTwig(
        string $path,
        array $guestPaths,
        array $adminPaths,
        bool $isAdminPath,
        bool $showHeader,
        bool $showFooter,
        bool $showAssistant,
    ): void {
        $this->twig->addGlobal('path', $path);
        $this->twig->addGlobal('guestPaths', $guestPaths);
        $this->twig->addGlobal('adminPaths', $adminPaths);
        $this->twig->addGlobal('isAdminPath', $isAdminPath);
        $this->twig->addGlobal('showHeader', $showHeader);
        $this->twig->addGlobal('showFooter', $showFooter);
        $this->twig->addGlobal('showAssistant', $showAssistant);
    }
}
