<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Ports\{
    Cache\Service\Query\DateCacheQueryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class DateSubscriber implements EventSubscriberInterface
{
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private DateCacheQueryContract $dateCacheQuery,
    ) {}

    /** @return array<class-string, string> */
    public static function getSubscribedEvents(): array
    {
        return [
            ControllerEvent::class => 'onKernelController',
        ];
    }

    public function onKernelController(ControllerEvent $event): void
    {
        if (!$event->isMainRequest()) {
            return;
        }

        try {
            $data = $this->dateCacheQuery->getCurrentData();

            $this->addGlobalVariablesToTwig($data->toArray());
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'DateSubscriber error while fetching values',
                $throwable,
            );

            $this->addGlobalVariablesToTwig([]);
        }
    }

    /** @param array<string, mixed> $values */
    private function addGlobalVariablesToTwig(array $values): void
    {
        foreach ($values as $key => $value) {
            $this->twig->addGlobal($key, $value);
        }
    }
}
