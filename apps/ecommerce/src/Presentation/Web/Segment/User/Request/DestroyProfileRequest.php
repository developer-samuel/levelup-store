<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\User\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Presentation\Abstract\Request\AbstractRequest;

final class DestroyProfileRequest extends AbstractRequest
{
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void {}

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('profile_destroy', $context);
    }
}
