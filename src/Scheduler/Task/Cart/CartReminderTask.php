<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Cart;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\{
    Component\Messenger\Attribute\AsMessageHandler,
    Component\Routing\Generator\UrlGeneratorInterface
};

use App\Core\Domain\Segment\Cart\Entity\Cart;

use App\Core\Ports\{
    Segment\Cart\Repository\CartRepositoryContract,
    Shared\Logging\ConsoleLoggerContract
};

use App\Core\Ports\Segment\Cart\Email\CartReminderEmailContract;

use App\Scheduler\{
    Message\Cart\CartReminderMessage,
    Task\Abstract\AbstractTask
};

#[AsMessageHandler]
class CartReminderTask extends AbstractTask
{
    private const INACTIVE_HOURS = 24;
    private const EXPIRY_DAYS = 7;

    /**
     * @param CartRepositoryContract $cartRepository
     * @param CartReminderEmailContract $cartReminderEmail
     * @param UrlGeneratorInterface $urlGenerator
     * @param EntityManagerInterface $entityManager
     * @param ConsoleLoggerContract $logger
    */
    public function __construct(
        private readonly CartRepositoryContract $cartRepository,
        private readonly CartReminderEmailContract $cartReminderEmail,
        private readonly UrlGeneratorInterface $urlGenerator,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    /**
     * @param CartReminderMessage $message
     *
     * @return void
    */
    public function __invoke(CartReminderMessage $message): void
    {
        $this->execute();
    }

    /**
     * @return string
    */
    protected function getTaskName(): string
    {
        return 'CartReminderTask';
    }

    /**
     * @return Cart[]
    */
    protected function fetchItems(): iterable
    {
        $from = new \DateTimeImmutable(sprintf('-%d hours', self::INACTIVE_HOURS));
        $to = new \DateTimeImmutable(sprintf('-%d days', self::EXPIRY_DAYS));

        return $this->cartRepository->findAbandonedForReminder($from, $to);
    }

    /**
     * @param iterable<Cart> $items
     *
     * @return int
    */
    protected function processItems(iterable $items): int
    {
        $count = 0;
        $cartUrl = $this->urlGenerator->generate('home', [], UrlGeneratorInterface::ABSOLUTE_URL);

        foreach ($items as $cart) {
            $daysRemaining = $this->calculateDaysRemaining($cart);

            $this->cartReminderEmail->send($cart->getUser(), $daysRemaining, $cartUrl);
            $cart->markReminderSent();

            $count++;
        }

        if ($count > 0) {
            $this->entityManager->flush();
        }

        return $count;
    }

    /**
     * @param Cart $cart
     *
     * @return int
    */
    private function calculateDaysRemaining(Cart $cart): int
    {
        $updatedAt = $cart->getUpdatedAt() ?? $cart->getCreatedAt();
        $expiresAt = $updatedAt->modify(sprintf('+%d days', self::EXPIRY_DAYS));
        $diff = (new \DateTimeImmutable())->diff($expiresAt);

        return max(1, $diff->days);
    }
}
