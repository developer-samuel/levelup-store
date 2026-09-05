<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Segment\User\Input\ChangePasswordInput;

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\PasswordCheckFields
};

use App\Shared\Traits\Identity\TokenTrait;

final class ChangePasswordRequest extends AbstractRequest
{
    use TokenTrait;
    use ChangePasswordInput;

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

        $this->old_password = $data->getString('old_password');
        $this->new_password = $data->getString('new_password');
        $this->new_password_confirmation = $data->getString('new_password_confirmation');
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('change_password_update', $context);
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
            'new_password',
            'new_password_confirmation',
            'New passwords do not match.',
        );
    }
}
