<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\Shared\ValueObject\AddressObject;

use App\Core\Application\{
    Segment\Order\Input\OrderInput,
    Shared\Input\Address\BillingAddressInput,
    Shared\Input\Address\ShippingAddressInput
};

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\AddressCheckFields
};

use App\Shared\Enum\AddressType;

final class OrderRequest extends AbstractRequest
{
    use OrderInput;
    use BillingAddressInput;
    use ShippingAddressInput;

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
    */
    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
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

        $this->email = DataSanitizer::sanitizeString($data->get('email'));
        $this->first_name = DataSanitizer::sanitizeString($data->get('first_name'));
        $this->last_name = DataSanitizer::sanitizeString($data->get('last_name'));
        $this->payment_method = DataSanitizer::sanitizeString($data->get('payment_method'));

        $this->billing_country = DataSanitizer::sanitizeInt($data->get('billing_country')) ?? 0;
        $this->billing_street = DataSanitizer::sanitizeString($data->get('billing_street'));
        $this->billing_postal_code = DataSanitizer::sanitizeString($data->get('billing_postal_code'));
        $this->billing_city = DataSanitizer::sanitizeString($data->get('billing_city'));

        $this->shipping_country = DataSanitizer::sanitizeInt($data->get('shipping_country')) ?? 0;
        $this->shipping_street = DataSanitizer::sanitizeString($data->get('shipping_street'));
        $this->shipping_postal_code = DataSanitizer::sanitizeString($data->get('shipping_postal_code'));
        $this->shipping_city = DataSanitizer::sanitizeString($data->get('shipping_city'));

        $this->send_shipping = $data->getBoolean('send_shipping');
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('orders_store', $context);
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateAddressFields(ExecutionContextInterface $context): void
    {
        AddressCheckFields::validateRequiredForType(
            $context,
            $this->buildBillingAddress(),
            AddressType::BILLING,
        );

        if ($this->send_shipping) {
            AddressCheckFields::validateRequiredForType(
                $context,
                $this->buildShippingAddress(),
                AddressType::SHIPPING,
            );
        }
    }

    /**
     * @return AddressObject
    */
    private function buildBillingAddress(): AddressObject
    {
        return new AddressObject(
            country: $this->sanitizeId($this->billing_country ?? 0),
            street: DataSanitizer::sanitizeString($this->billing_street ?? ''),
            postalCode: DataSanitizer::sanitizeString($this->billing_postal_code ?? ''),
            city: DataSanitizer::sanitizeString($this->billing_city ?? ''),
            sendShipping: false,
        );
    }

    /**
     * @return AddressObject
    */
    private function buildShippingAddress(): AddressObject
    {
        return new AddressObject(
            country: $this->sanitizeId($this->shipping_country ?? 0),
            street: DataSanitizer::sanitizeString($this->shipping_street ?? ''),
            postalCode: DataSanitizer::sanitizeString($this->shipping_postal_code ?? ''),
            city: DataSanitizer::sanitizeString($this->shipping_city ?? ''),
            sendShipping: true,
        );
    }

    /**
     * @param int $id
     *
     * @return string
    */
    private function sanitizeId(int $id): string
    {
        return $id > 0 ? (string) $id : '';
    }
}
