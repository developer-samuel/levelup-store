<?php

declare(strict_types=1);

namespace App\Presentation\Api\Auth;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\Security\Csrf\CsrfTokenManagerInterface
};

use App\Core\Application\Auth\Input\LoginInput;

use App\Presentation\Abstract\Request\AbstractRequest;

final class LoginRequest extends AbstractRequest
{
    use LoginInput;

    public function __construct(CsrfTokenManagerInterface $csrfTokenManager)
    {
        parent::__construct($csrfTokenManager);
    }
    
    protected function populateData(Request $request): void
    {
        $decoded = json_decode($request->getContent(), true);

        /** @var array<string, string> $data */
        $data = is_array($decoded) ? $decoded : [];

        $this->email = trim((string) ($data['email'] ?? ''));
        $this->password = (string) ($data['password'] ?? '');
    }
}
