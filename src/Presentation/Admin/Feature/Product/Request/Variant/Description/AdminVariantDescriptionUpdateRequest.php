<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Request\Variant\Description;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Admin\Segment\Product\Input\Description\AdminVariantDescriptionUpdateInput;

use App\Presentation\Abstract\Request\AbstractRequest;

class AdminVariantDescriptionUpdateRequest extends AbstractRequest
{
    use AdminVariantDescriptionUpdateInput;

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
        $this->position = $data->getInt('position', 1);
        $this->title = trim($data->getString('title'));
        $this->body = trim($data->getString('body'));
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('admin_variants_descriptions_update', $context);
    }
}
