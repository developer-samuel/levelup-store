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
