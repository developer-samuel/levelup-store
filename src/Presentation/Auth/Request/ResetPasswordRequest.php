<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Auth\Input\ResetPasswordInput;

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\PasswordCheckFields
};

use App\Shared\Traits\Identity\TokenTrait;

final class ResetPasswordRequest extends AbstractRequest
{
    use TokenTrait;
    use ResetPasswordInput;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
    */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    /**
     * @param Request $request
     *
     * @return void
    */
    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->token = $data->getString('token');
        $this->password = $data->getString('password');
        $this->password_confirmation = $data->getString('password_confirmation');
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('reset_password_store', $context);
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validatePasswordsMatch(ExecutionContextInterface $context): void
    {
        PasswordCheckFields::validatePasswordsMatch(
            $context,
            $this,
            'password',
            'password_confirmation',
        );
    }
}
