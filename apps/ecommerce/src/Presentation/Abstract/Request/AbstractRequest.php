<?php

declare(strict_types=1);

namespace App\Presentation\Abstract\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface,
    Component\Validator\Validator\ValidatorInterface
};

use App\Presentation\Shared\Traits\CsrfProtection;

abstract class AbstractRequest
{
    use CsrfProtection;

    public function __construct(
        protected CsrfTokenManagerInterface $csrfTokenManager,
    ) {}

    abstract protected function populateData(Request $request): void;

    protected final function resolveCsrfTokenManager(): CsrfTokenManagerInterface
    {
        return $this->csrfTokenManager;
    }

    /** @return array<string, string> */
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
