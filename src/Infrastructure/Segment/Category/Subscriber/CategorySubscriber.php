<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Category\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Domain\Segment\Category\Entity\Category;

use App\Core\Ports\{
    Segment\Category\Repository\CategoryRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class CategorySubscriber implements EventSubscriberInterface
{
    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
     * @param CategoryRepositoryContract $categoryRepository
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private CategoryRepositoryContract $categoryRepository,
    ) {}

    /**
     * @return array<string, string>
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
            $categories = $this->categoryRepository->findAll();

            $this->addGlobalVariablesToTwig($categories);
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'CategorySubscriber error while fetching values',
                $throwable,
            );

            $this->addGlobalVariablesToTwig([]);
        }
    }

    /**
     * @param Category[] $categories
     *
     * @return void
    */
    private function addGlobalVariablesToTwig(array $categories): void
    {
        $this->twig->addGlobal('categories', $categories);
    }
}
