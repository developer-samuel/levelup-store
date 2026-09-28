<?php

declare(strict_types=1);

namespace App\Infrastructure\Shared\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Ports\{
    Cache\Service\Query\FilterCacheQueryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class FilterSubscriber implements EventSubscriberInterface
{
    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
     * @param FilterCacheQueryContract $filterCacheQuery
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private FilterCacheQueryContract $filterCacheQuery,
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
            $queryString = $event->getRequest()->getQueryString() ?? '';

            $filterData = $this->filterCacheQuery->getVars($queryString);

            $this->setGlobalVariablesInTwig(
                $filterData->subtypesActive,
                $filterData->brandsActive,
                $filterData->step,
            );
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'FilterSubscriber error while fetching values',
                $throwable,
            );

            $this->setGlobalVariablesInTwig([], [], 1);
        }
    }

    /**
     * @param array<int, string> $subtypesActive
     * @param array<int, string> $brandsActive
     * @param int $step
     *
     * @return void
    */
    private function setGlobalVariablesInTwig(array $subtypesActive, array $brandsActive, int $step): void
    {
        $this->twig->addGlobal('filterSubtypesActive', $subtypesActive);
        $this->twig->addGlobal('filterBrandsActive', $brandsActive);
        $this->twig->addGlobal('filterPriceStep', $step);
    }
}
