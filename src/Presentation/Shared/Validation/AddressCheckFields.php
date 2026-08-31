<?php

declare(strict_types=1);

namespace App\Presentation\Shared\Validation;

use Symfony\Component\Validator\Context\ExecutionContextInterface;

use Kit\Utils\Shared\Normalizer\StringNormalizer;

use App\Core\Domain\Shared\ValueObject\AddressObject;

use App\Shared\{
    Enum\AddressFields,
    Enum\AddressType
};

/**
 * @phpstan-type AddressRule array{
 *     0:int|null,
 *     1:int|null,
 *     2:string|null
 * }
*/
final class AddressCheckFields
{
    /**
     * @param ExecutionContextInterface $context
     * @param AddressObject $address
     * @param bool|null $sendShipping
     *
     * @return void
    */
    public static function validateRequired(
        ExecutionContextInterface $context,
        AddressObject $address,
        ?bool $sendShipping = null,
    ): void {
        self::validateWithRequiredFlag($context, $address, true, $sendShipping);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param AddressObject $address
     * @param bool|null $sendShipping
     *
     * @return void
    */
    public static function validateOptional(
        ExecutionContextInterface $context,
        AddressObject $address,
        ?bool $sendShipping = null,
    ): void {
        self::validateWithRequiredFlag($context, $address, false, $sendShipping);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param AddressObject $address
     * @param AddressType $type
     *
     * @return void
    */
    public static function validateRequiredForType(
        ExecutionContextInterface $context,
        AddressObject $address,
        AddressType $type,
    ): void {
        self::validateWithRequiredFlag($context, $address, true, null, $type);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param AddressObject $address
     * @param bool $required
     * @param bool|null $sendShipping
     *
     * @return void
    */
    private static function validateWithRequiredFlag(
        ExecutionContextInterface $context,
        AddressObject $address,
        bool $required,
        ?bool $sendShipping = null,
        ?AddressType $type = null,
    ): void {
        $rules = self::getRules();
        $types = $type !== null ? [$type] : [AddressType::BILLING, AddressType::SHIPPING];

        foreach ($types as $t) {
            if ($t === AddressType::SHIPPING && $sendShipping === false) {
                continue;
            }

            self::validateAddress($context, $address, $t, $rules, $required);
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param AddressObject $address
     * @param AddressType $type
     * @param array<string, AddressRule> $rules
     * @param bool $required
     *
     * @return void
    */
    private static function validateAddress(
        ExecutionContextInterface $context,
        AddressObject $address,
        AddressType $type,
        array $rules,
        bool $required,
    ): void {
        if (self::shouldSkipShipping($type, $address)) {
            return;
        }

        foreach ($rules as $field => [$min, $max, $regex]) {
            $fieldEnum = AddressFields::tryFrom($field) ?? AddressFields::COUNTRY;

            $value = self::getPropertyValue($address, $fieldEnum);
            $propertyName = sprintf('%s_%s', strtolower($type->name), strtolower($fieldEnum->name));

            if (self::isEmptyAndRequired($value, $required)) {
                self::addRequiredViolation($context, $propertyName, $field);
                continue;
            }

            if (!self::shouldApplyRules($value, $required)) {
                continue;
            }

            self::applyRules($context, $propertyName, $value, $min, $max, $regex);
        }
    }

    /**
     * @param AddressObject $address
     * @param AddressFields $field
     *
     * @return string
    */
    private static function getPropertyValue(AddressObject $address, AddressFields $field): string
    {
        return match($field) {
            AddressFields::COUNTRY     => $address->country,
            AddressFields::STREET      => $address->street,
            AddressFields::POSTAL_CODE => $address->postalCode,
            AddressFields::CITY        => $address->city,
        };
    }

    /**
     * @param AddressType $type
     * @param AddressObject $address
     *
     * @return bool
    */
    private static function shouldSkipShipping(AddressType $type, AddressObject $address): bool
    {
        return $type === AddressType::SHIPPING && ($address->sendShipping ?? false) === false;
    }

    /**
     * @param string $value
     * @param bool $required
     *
     * @return bool
    */
    private static function isEmptyAndRequired(string $value, bool $required): bool
    {
        return $required && $value === '';
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $property
     * @param string $field
     *
     * @return void
    */
    private static function addRequiredViolation(
        ExecutionContextInterface $context,
        string $property,
        string $field,
    ): void {
        $context->buildViolation(sprintf('%s is required.', self::getLabel($field)))
            ->atPath($property)
            ->addViolation();
    }

    /**
     * @param string $value
     * @param bool $required
     *
     * @return bool
    */
    private static function shouldApplyRules(string $value, bool $required): bool
    {
        return $required || trim($value) !== '';
    }

    /**
     * @param string $field
     *
     * @return string
    */
    private static function getLabel(string $field): string
    {
        $case = AddressFields::tryFrom($field);
        if ($case !== null) {
            return $case->getLabel();
        }

        $normalized = StringNormalizer::replaceUnderscoresWithSpaces($field);

        return StringNormalizer::capitalizeWords($normalized);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $property
     * @param string $value
     * @param int|null $min
     * @param int|null $max
     * @param string|null $regex
     *
     * @return void
    */
    private static function applyRules(
        ExecutionContextInterface $context,
        string $property,
        string $value,
        ?int $min,
        ?int $max,
        ?string $regex,
    ): void {
        $field = explode('_', $property, 2)[1];
        $label = self::getLabel($field);

        self::validateMinLength($context, $property, $value, $min, $label);
        self::validateMaxLength($context, $property, $value, $max, $label);
        self::validateRegex($context, $property, $value, $regex, $label);
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $property
     * @param string $value
     * @param int|null $min
     * @param string $label
     *
     * @return void
    */
    private static function validateMinLength(
        ExecutionContextInterface $context,
        string $property,
        string $value,
        ?int $min,
        string $label,
    ): void {
        if ($min !== null && strlen($value) < $min) {
            $context->buildViolation(sprintf('%s must be at least %d characters long.', $label, $min))
                ->atPath($property)
                ->addViolation();
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $property
     * @param string $value
     * @param int|null $max
     * @param string $label
     *
     * @return void
    */
    private static function validateMaxLength(
        ExecutionContextInterface $context,
        string $property,
        string $value,
        ?int $max,
        string $label,
    ): void {
        if ($max !== null && strlen($value) > $max) {
            $context->buildViolation(sprintf('%s must not exceed %d characters.', $label, $max))
                ->atPath($property)
                ->addViolation();
        }
    }

    /**
     * @param ExecutionContextInterface $context
     * @param string $property
     * @param string $value
     * @param string|null $regex
     * @param string $label
     *
     * @return void
    */
    private static function validateRegex(
        ExecutionContextInterface $context,
        string $property,
        string $value,
        ?string $regex,
        string $label,
    ): void {
        if ($regex && !preg_match($regex, $value)) {
            $context->buildViolation(sprintf('%s contains invalid characters.', $label))
                ->atPath($property)
                ->addViolation();
        }
    }

    /**
     * @return array<string, AddressRule>
    */
    private static function getRules(): array
    {
        return [
            'country'     => [null, 100, null],
            'street'      => [null, 200, null],
            'postal_code' => [3, 15, '/^[A-Za-z0-9\s\-]+$/'],
            'city'        => [2, 100, null],
        ];
    }
}
