<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Traits;

use Symfony\{
    Component\Security\Csrf\CsrfToken,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Context\ExecutionContextInterface
};

trait CsrfProtection
{
    public string $csrfToken;

    abstract protected function resolveCsrfTokenManager(): CsrfTokenManagerInterface;

    public function validateCsrfToken(string $tokenId, ExecutionContextInterface $context): void
    {
        if ($this->csrfToken !== '' && !$this->resolveCsrfTokenManager()->isTokenValid(new CsrfToken($tokenId, $this->csrfToken))) {
            $context->buildViolation('Invalid CSRF token.')
                ->atPath('csrf_token')
                ->addViolation();
        }
    }
}
