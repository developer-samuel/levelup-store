<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use Kit\Utils\Shared\DataSanitizer;

use App\Presentation\Shared\Traits\CsrfProtection;

abstract class AbstractRequest
{
    use CsrfProtection;

    private const INT_FIELDS = ['billing_country', 'shipping_country'];

    /**
     * @param CsrfTokenManagerInterface $csrfTokenManager
    */
    public function __construct(
        protected CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    /**
     * @param Request $request
     *
     * @return void
    */
    abstract protected function populateData(Request $request): void;

    /**
     * @return CsrfTokenManagerInterface
    */
    protected final function resolveCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->csrfTokenManager;
    }

    /**
     * @param Request $request
     * @param string $field
     *
     * @return void
    */
    protected final function extractTypedField(Request $request, string $field): void
    {
        if (!property_exists($this, $field)) {
            return;
        }

        $rawValue = $request->request->get($field);

        if (in_array($field, self::INT_FIELDS, true)) {
            $this->{$field} = DataSanitizer::sanitizeInt($rawValue) ?? 0;

            return;
        }

        $this->{$field} = DataSanitizer::sanitizeString($rawValue);
    }

    /**
     * @param ValidatorInterface|null $validator
     *
     * @return array<string, string>
    */
    final public function errors(?ValidatorInterface $validator): array
    {
        if ($validator === null) {
            return [];
        }

        $errors = $validator->validate($this);
        $result = [];

        foreach ($errors as $error) {
            $result[$error->getPropertyPath()] = (string) $error->getMessage();
        }

        return $result;
    }

    /**
     * @param Request $request
     * @param CsrfTokenManagerInterface $csrfTokenManager
     *
     * @return static
    */
    public static function fromHttpRequest(
        Request $request,
        CsrfTokenManagerInterface $csrfTokenManager,
    ): static {
        /** @phpstan-ignore-next-line */
        $instance = new static($csrfTokenManager);
        $instance->csrfToken = $request->request->getString('_csrf_token', '');
        $instance->populateData($request);

        return $instance;
    }
}
