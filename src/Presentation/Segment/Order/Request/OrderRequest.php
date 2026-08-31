<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Order\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use Kit\Utils\Shared\Sanitizer\DataSanitizer;

use App\Core\Application\{
    Segment\Order\Input\OrderInput,
    Shared\Input\Address\BillingAddressInput,
    Shared\Input\Address\ShippingAddressInput
};

use App\Core\Domain\{
    Segment\Order\Fields\OrderFields,
    Shared\ValueObject\AddressObject
};

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\AddressCheckFields
};

use App\Shared\{
    Enum\AddressType,
    Utils\Resolver\AddressResolver
};

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

        $fieldsToProcess = array_unique(array_merge(
            OrderFields::required(),
            ['payment_method'],
        ));

        foreach ($fieldsToProcess as $field) {
            $this->extractTypedField($request, $field);
        }

        $shippingFields = AddressResolver::for(AddressType::SHIPPING);
        foreach ($shippingFields as $field) {
            $this->extractTypedField($request, $field);
        }

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
