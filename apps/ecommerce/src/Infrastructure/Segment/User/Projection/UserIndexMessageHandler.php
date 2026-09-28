<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Projection;

use Symfony\Component\Messenger\Attribute\AsMessageHandler;

use App\Core\Domain\Segment\User\Message\UserIndexMessage;

use App\Core\Ports\Segment\User\Repository\UserRepositoryContract;

#[AsMessageHandler]
final readonly class UserIndexMessageHandler
{
    /**
     * @param UserRepositoryContract $userRepository
     * @param UserProjector $projector
    */
    public function __construct(
        private UserRepositoryContract $userRepository,
        private UserProjector $projector,
    ) {}

    /**
     * @param UserIndexMessage $message
     *
     * @return void
    */
    public function __invoke(UserIndexMessage $message): void
    {
        $user = $this->userRepository->findById($message->userId);

        if ($user === null) {
            return;
        }

        $this->projector->index($user);
    }
}
