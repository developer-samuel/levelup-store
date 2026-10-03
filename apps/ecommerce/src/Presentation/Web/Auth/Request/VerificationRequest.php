<?php

declare(strict_types=1);

namespace App\Presentation\Web\Auth\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Presentation\Abstract\Request\AbstractRequest;

final class VerificationRequest extends AbstractRequest
{
    public ?string $email = null;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->email = trim($data->getString('email'));
    }

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('verification_store', $context);
    }
}
