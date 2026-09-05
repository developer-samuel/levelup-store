<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Request\Variant\Ean;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Admin\Segment\Product\Input\Ean\AdminVariantEanUpdateInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class AdminVariantEanUpdateRequest extends AbstractRequest
{
    use AdminVariantEanUpdateInput;

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

        $this->id = $data->getString('id');
        $this->variantId = $data->getString('variant_id');
        $this->code = trim($data->getString('code'));
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('admin_variants_eans_update', $context);
    }
}
