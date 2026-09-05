<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Brand\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Admin\Segment\Brand\Input\AdminBrandStoreInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class AdminBrandStoreRequest extends AbstractRequest
{
    use AdminBrandStoreInput;

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

        $this->name = trim($data->getString('name'));
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('admin_brands_store', $context);
    }
}
