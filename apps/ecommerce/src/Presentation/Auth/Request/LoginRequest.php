<?php

declare(strict_types=1);

namespace App\Presentation\Auth\Request;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Core\Application\Auth\Input\LoginInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class LoginRequest extends AbstractRequest
{
    use LoginInput;

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
        $decoded = json_decode($request->getContent(), true);

        /** @var array<string, string> $data */
        $data = is_array($decoded) ? $decoded : [];

        $this->email = trim((string) ($data['email'] ?? ''));
        $this->password = (string) ($data['password'] ?? '');
    }
}
