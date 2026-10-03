<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\Review\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Application\Segment\Review\Input\ReviewDestroyInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class ReviewDestroyRequest extends AbstractRequest
{
    use ReviewDestroyInput;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager) {
        parent::__construct($csrfTokenManager);
    }

    protected function populateData(Request $request): void
    {
        $data = $request->request;

        $this->reviewId = $data->getString('reviewId');
    }

    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('reviews_destroy', $context);
    }
}
