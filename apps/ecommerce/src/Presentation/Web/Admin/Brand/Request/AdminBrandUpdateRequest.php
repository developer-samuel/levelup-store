<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Brand\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Admin\Segment\Brand\Input\AdminBrandUpdateInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class AdminBrandUpdateRequest extends AbstractRequest
{
    use AdminBrandUpdateInput;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->id = $data->getString('id');
        $this->name = trim($data->getString('name'));
    }

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('admin_brands_update', $context);
    }
}
