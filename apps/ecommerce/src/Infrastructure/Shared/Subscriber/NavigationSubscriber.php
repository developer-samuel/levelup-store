<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Ports\Shared\Logging\AppLoggerContract;

final readonly class NavigationSubscriber implements EventSubscriberInterface
{
    private const MAX_TYPES = 6;

    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
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

        try {
            $this->setGlobalVariablesInTwig();
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'NavigationSubscriber error while fetching values',
                $throwable,
            );

            $this->setGlobalVariablesInTwig();
        }
    }

    /**
     * @return void
    */
    private function setGlobalVariablesInTwig(): void
    {
        $this->twig->addGlobal('maxTypes', self::MAX_TYPES);
    }
}
