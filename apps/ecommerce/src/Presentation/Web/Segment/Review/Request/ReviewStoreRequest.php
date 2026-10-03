<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Review\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use Packages\Kit\Utils\Shared\DataSanitizer;

use App\Core\Application\Segment\Review\Input\ReviewStoreInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class ReviewStoreRequest extends AbstractRequest
{
    use ReviewStoreInput;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->variantId = $data->getString('variantId');
        $this->value = DataSanitizer::sanitizeFloat($data->get('value')) ?? 0.0;
        $this->body = trim($data->getString('body'));
        $this->positives = DataSanitizer::sanitizeStringArray($data->all('positives'));
        $this->negatives = DataSanitizer::sanitizeStringArray($data->all('negatives'));
    }

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('reviews_store', $context);
    }
}
