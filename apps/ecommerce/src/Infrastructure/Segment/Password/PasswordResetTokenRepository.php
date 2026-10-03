<?php

declare(strict_types=1);

namespace App\Infrastructure\Segment\Password;

use Doctrine\Persistence\ManagerRegistry;

use App\Core\Domain\Segment\Password\Entity\PasswordResetToken;

use App\Core\Ports\Segment\Password\PasswordResetTokenRepositoryContract;

use App\Infrastructure\Abstract\Repository\AbstractTokenRepository;

/** @extends AbstractTokenRepository<PasswordResetToken> */
final class PasswordResetTokenRepository extends AbstractTokenRepository implements PasswordResetTokenRepositoryContract
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct(
            $registry,
            PasswordResetToken::class,
        );
    }

    public function findByToken(string $token): ?PasswordResetToken
    {
        return parent::findByToken($token);
    }

    protected function getAlias(): string
    {
        return 'prt';
    }
}
