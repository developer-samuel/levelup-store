<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\User\Repository;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\User\Entity\UserVerificationToken;

use App\Core\Ports\Segment\User\Repository\UserVerificationTokenRepositoryContract;

use App\Infrastructure\Abstract\Repository\AbstractTokenRepository;

/** @extends AbstractTokenRepository<UserVerificationToken> */
final class UserVerificationTokenRepository extends AbstractTokenRepository implements UserVerificationTokenRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            UserVerificationToken::class,
        );
    }

    public function findByToken(string $token): ?UserVerificationToken
    {
        return parent::findByToken($token);
    }

    protected function getAlias(): string
    {
        return 'uvt';
    }
}
