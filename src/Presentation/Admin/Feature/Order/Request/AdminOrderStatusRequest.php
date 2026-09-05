<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Order\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use App\Core\Domain\Segment\Order\Enum\OrderStatus;

use App\Core\Application\Admin\Segment\Order\Input\AdminOrderStatusInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class AdminOrderStatusRequest extends AbstractRequest
{
    use AdminOrderStatusInput;

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
        $query = $request->query;

        $this->status = OrderStatus::tryFrom($data->getString('status')) ?? OrderStatus::PENDING;

        $rawCode = $query->get('code') ?? $data->getString('code');

        $this->code = trim($rawCode);
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('admin_orders_status_update', $context);
    }
}
