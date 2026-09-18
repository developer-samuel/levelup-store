<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Footer\Subscriber;

use Symfony\{
    Component\EventDispatcher\EventSubscriberInterface,
    Component\HttpKernel\Event\ControllerEvent
};

use Twig\Environment;

use App\Core\Domain\{
    Segment\Footer\Entity\FooterLink,
    Segment\Footer\Enum\FooterLinkGroup
};

use App\Core\Ports\{
    Segment\Footer\Repository\FooterLinkRepositoryContract,
    Shared\Logging\AppLoggerContract
};

final readonly class FooterSubscriber implements EventSubscriberInterface
{
    /**
     * @param Environment $twig
     * @param AppLoggerContract $logger
     * @param FooterLinkRepositoryContract $footerLinkRepository
    */
    public function __construct(
        private Environment $twig,
        private AppLoggerContract $logger,
        private FooterLinkRepositoryContract $footerLinkRepository,
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
            $links = $this->footerLinkRepository->findAllOrderedByGroup();

            $this->addGlobalVariablesToTwig($this->groupByEnum($links));
        } catch (\Throwable $throwable) {
            $this->logger->alert(
                'FooterSubscriber error while fetching values',
                $throwable,
            );

            $this->addGlobalVariablesToTwig([]);
        }
    }

    /**
     * @param FooterLink[] $links
     *
     * @return array<string, FooterLink[]>
    */
    private function groupByEnum(array $links): array
    {
        $grouped = [];

        foreach (FooterLinkGroup::cases() as $group) {
            $grouped[$group->value] = [];
        }

        foreach ($links as $link) {
            $grouped[$link->getGroup()->value][] = $link;
        }

        return $grouped;
    }

    /**
     * @param array<string, FooterLink[]> $footerLinks
     *
     * @return void
    */
    private function addGlobalVariablesToTwig(array $footerLinks): void
    {
        $this->twig->addGlobal('footerLinks', $footerLinks);
    }
}
