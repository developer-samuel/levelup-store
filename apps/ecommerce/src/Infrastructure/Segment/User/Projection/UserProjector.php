<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Projection;

use App\Core\Domain\Segment\User\Entity\User;

use App\Core\Ports\{
    Gateways\External\Search\ElasticsearchGatewayContract,
    Segment\User\Repository\UserRepositoryContract,
    Shared\ReindexableInterface
};

final readonly class UserProjector implements ReindexableInterface
{
    public function __construct(
        private ElasticsearchGatewayContract $elasticsearch,
        private UserRepositoryContract $userRepository,
    ) {}

    public function index(User $user): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->indexDocument(UserProjection::NAME, $user->getId(), $this->buildDocument($user));
    }

    public function remove(User $user): void
    {
        if (!$this->elasticsearch->isEnabled()) {
            return;
        }

        $this->elasticsearch->removeDocument(UserProjection::NAME, $user->getId());
    }

    public function reindexAll(): int
    {
        $this->elasticsearch->ensureIndexExists(UserProjection::NAME, UserProjection::mapping());

        $users = $this->userRepository->findAll();
        $indexed = 0;

        foreach ($users as $user) {
            $this->index($user);
            ++$indexed;
        }

        return $indexed;
    }

    public function getIndexName(): string
    {
        return UserProjection::NAME;
    }

    /** @return array<string, mixed> */
    private function buildDocument(User $user): array
    {
        return [
            'email'      => $user->getEmail(),
            'first_name' => $user->getFirstName(),
            'last_name'  => $user->getLastName(),
            'role'       => $user->getRole()->value,
            'created_at' => $user->getCreatedAt()->format(\DateTimeInterface::ATOM),
        ];
    }
}
