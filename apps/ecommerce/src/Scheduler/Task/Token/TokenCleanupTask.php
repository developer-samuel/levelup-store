<?php

declare(strict_types=1);

namespace App\Scheduler\Task\Token;

use Doctrine\ORM\EntityManagerInterface;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Ports\{
    Auth\Repository\RefreshTokenRepositoryContract,
    Segment\Password\PasswordResetTokenRepositoryContract,
    Segment\User\Repository\UserVerificationTokenRepositoryContract,
    Shared\Logging\ConsoleLoggerContract,
    Shared\Repository\CleanableTokenRepositoryContract
};

use App\Scheduler\{
    Message\Token\TokenCleanupMessage,
    Task\Abstract\AbstractTask
};

/** @extends AbstractTask<CleanableTokenRepositoryContract> */
#[AsMessageHandler]
final class TokenCleanupTask extends AbstractTask
{
    public function __construct(
        private readonly RefreshTokenRepositoryContract $refreshTokenRepository,
        private readonly PasswordResetTokenRepositoryContract $passwordResetTokenRepository,
        private readonly UserVerificationTokenRepositoryContract $userVerificationTokenRepository,
        EntityManagerInterface $entityManager,
        ConsoleLoggerContract $logger,
    ) {
        parent::__construct($entityManager, $logger);
    }

    public function __invoke(TokenCleanupMessage $message): void
    {
        $this->execute();
    }

    protected function getTaskName(): string
    {
        return 'TokenCleanupTask';
    }

    /** @return iterable<CleanableTokenRepositoryContract> */
    protected function fetchItems(): iterable
    {
        return [
            $this->refreshTokenRepository,
            $this->passwordResetTokenRepository,
            $this->userVerificationTokenRepository,
        ];
    }

    /** @param iterable<CleanableTokenRepositoryContract> $items */
    protected function processItems(iterable $items): int
    {
        $deletedCount = 0;

        foreach ($items as $repository) {
            $deletedCount += $repository->deleteExpired();
        }

        return $deletedCount;
    }
}
