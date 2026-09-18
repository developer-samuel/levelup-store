<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Constraints as Assert,
    Component\Validator\Context\ExecutionContextInterface
};

use Kit\Utils\Shared\DataSanitizer;

use App\Core\Domain\Shared\ValueObject\AddressObject;

use App\Core\Application\{
    Segment\User\Input\UserPersonalInput,
    Shared\Input\Address\BillingAddressInput,
    Shared\Input\Address\ShippingAddressInput
};

use App\Presentation\{
    Abstract\Request\AbstractRequest,
    Shared\Validation\AddressCheckFields
};

use App\Shared\Enum\AddressType;

final class ProfileRequest extends AbstractRequest
{
    use UserPersonalInput;
    use BillingAddressInput;
    use ShippingAddressInput;

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

        $this->first_name = DataSanitizer::sanitizeString($data->get('first_name'));
        $this->last_name  = DataSanitizer::sanitizeString($data->get('last_name'));

        $this->billing_country     = DataSanitizer::sanitizeInt($data->get('billing_country')) ?? 0;
        $this->billing_street      = DataSanitizer::sanitizeString($data->get('billing_street'));
        $this->billing_postal_code = DataSanitizer::sanitizeString($data->get('billing_postal_code'));
        $this->billing_city        = DataSanitizer::sanitizeString($data->get('billing_city'));

        $this->shipping_country     = DataSanitizer::sanitizeInt($data->get('shipping_country')) ?? 0;
        $this->shipping_street      = DataSanitizer::sanitizeString($data->get('shipping_street'));
        $this->shipping_postal_code = DataSanitizer::sanitizeString($data->get('shipping_postal_code'));
        $this->shipping_city        = DataSanitizer::sanitizeString($data->get('shipping_city'));

        $this->use_shipping = $data->getBoolean('use_shipping');
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateCsrf(ExecutionContextInterface $context): void
    {
        $this->validateCsrfToken('profile_update', $context);
    }

    /**
     * @param ExecutionContextInterface $context
     *
     * @return void
    */
    #[Assert\Callback]
    public function validateOptionalAddressFields(ExecutionContextInterface $context): void
    {
        AddressCheckFields::validateOptionalForType($context, $this->createAddress(AddressType::SHIPPING), AddressType::SHIPPING, $this->use_shipping ?? false);
    }

    /**
     * @param AddressType $type
     *
     * @return AddressObject
    */
    private function createAddress(AddressType $type): AddressObject
    {
        return match ($type) {
            AddressType::BILLING => new AddressObject(
                country: DataSanitizer::sanitizeString($this->billing_country ?? ''),
                street: DataSanitizer::sanitizeString($this->billing_street ?? ''),
                postalCode: DataSanitizer::sanitizeString($this->billing_postal_code ?? ''),
                city: DataSanitizer::sanitizeString($this->billing_city ?? ''),
                sendShipping: DataSanitizer::sanitizeBoolean($this->use_shipping ?? false),
            ),
            AddressType::SHIPPING => new AddressObject(
                country: DataSanitizer::sanitizeString($this->shipping_country ?? ''),
                street: DataSanitizer::sanitizeString($this->shipping_street ?? ''),
                postalCode: DataSanitizer::sanitizeString($this->shipping_postal_code ?? ''),
                city: DataSanitizer::sanitizeString($this->shipping_city ?? ''),
                sendShipping: DataSanitizer::sanitizeBoolean($this->use_shipping ?? false),
            ),
        };
    }
}
