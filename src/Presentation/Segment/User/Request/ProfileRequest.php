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

use App\Shared\{
    Enum\AddressType,
    Utils\Resolver\AddressResolver
};

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
        foreach (['first_name', 'last_name'] as $field) {
            $this->extractTypedField($request, $field);
        }

        foreach ([AddressType::BILLING, AddressType::SHIPPING] as $type) {
            foreach (AddressResolver::for($type) as $field) {
                $this->extractTypedField($request, $field);
            }
        }

        $this->use_shipping = $request->request->getBoolean('use_shipping');
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
        AddressCheckFields::validateOptional($context, $this->createAddress(AddressType::SHIPPING), $this->use_shipping ?? false);
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
