<?php

declare(strict_types=1);

namespace App\Core\Application\Auth\Service\Command;

use App\Core\Domain\{
    Auth\Payload\UpdateVerificationPayload,
    Segment\User\Entity\User,
    Segment\User\Entity\UserVerificationToken
};

use App\Core\Application\Shared\Utils\CodeGenerator;

use App\Core\Ports\{
    Auth\Notifier\VerificationNotifierContract,
    Auth\Service\Command\VerificationCommandContract,
    Auth\Service\Query\VerificationQueryContract,
    Segment\User\Repository\UserVerificationTokenRepositoryContract,
    Shared\Persistence\EntityPersistenceContract
};

final readonly class VerificationCommandService implements VerificationCommandContract
{
    public function __construct(
        private EntityPersistenceContract $entityPersistence,
        private UserVerificationTokenRepositoryContract $tokenRepository,
        private VerificationQueryContract $verificationQuery,
        private VerificationNotifierContract $notifier,
    ) {}

    public function createAndSaveTokenForUser(User $user): void
    {
        $this->tokenRepository->removeTokensByUser($user);

        $token = CodeGenerator::generateUnique(128);
        $expiresAt = new \DateTimeImmutable('+15 minutes');

        $userVerificationToken = $this->createTokenEntity($user, $token, $expiresAt);

        $this->entityPersistence->persist($userVerificationToken, true);

        $this->notifier->send($user, $token);
    }

    public function verifyUserByToken(UpdateVerificationPayload $payload): ?User
    {
        $userVerificationToken = $this->verificationQuery->getValidToken($payload->token);
        if ($userVerificationToken === null) {
            return null;
        }

        $user = $userVerificationToken->getUser();
        if (!$this->verificationQuery->isUserVerifiable($user)) {
            return null;
        }

        $this->verifyUser($user);
        $this->tokenRepository->removeTokensByUser($user);

        return $user;
    }

    private function createTokenEntity(
        User $user,
        string $token,
        \DateTimeImmutable $expiresAt,
    ): UserVerificationToken {
        return (new UserVerificationToken())
            ->setUser($user)
            ->setToken($token)
            ->setExpiresAt($expiresAt);
    }

    private function verifyUser(User $user): void
    {
        $user->setEmailVerifiedAt(new \DateTimeImmutable());

        $this->entityPersistence->persist($user, true);
    }
}
