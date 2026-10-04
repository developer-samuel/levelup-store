<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Cart\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Segment\Cart\Input\CartStoreInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class CartStoreRequest extends AbstractRequest
{
    use CartStoreInput;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->variantId = $data->getInt('variant_id');
    }

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('cart_store', $context);
    }
}
