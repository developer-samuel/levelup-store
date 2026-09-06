<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Auth\Input\SignupInput;

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\PasswordCheckFields
};

final class SignupRequest extends AbstractRequest
{
    use SignupInput;

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

        $this->first_name = trim($data->getString('first_name'));
        $this->last_name = trim($data->getString('last_name'));
        $this->email = trim($data->getString('email'));
        $this->password = $data->getString('password');
        $this->password_confirmation = $data->getString('password_confirmation');

        $this->terms_and_conditions = $data->getBoolean('terms_and_conditions');
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('signup_store', $context);
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
