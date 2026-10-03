<?php

declare(strict_types=1);

namespace App\Presentation\Web\Segment\User\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Country\Entity\Country,
    Segment\User\Entity\User,
    Segment\User\Entity\UserBilling,
    Segment\User\Entity\UserShipping
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Country\Service\CountryCacheQueryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

use App\Shared\Responder\ErrorResponder;

final class ProfileQueryController extends AbstractQueryController
{
    public function __construct(
        private readonly CountryCacheQueryContract $countryCacheQuery,
        private readonly ErrorResponder $errorResponder,
        SecurityProviderContract $securityProvider,
        ExceptionResponder $exceptionResponder,
        AppLoggerContract $logger,
    ) {
        parent::__construct(
            $securityProvider,
            $exceptionResponder,
            $logger,
        );
    }

    public function show(): Response
    {
        $user = $this->securityProvider->getCurrentUser();

        if ($user === null) {
            return $this->errorResponder->renderUnauthorized();
        }

        $countries = $this->countryCacheQuery->getAllCountries();

        $billing = $user->getBilling() ?? null;
        $shipping = $user->getShipping() ?? null;

        return $this->renderProfilePage($user, $countries, $billing, $shipping);
    }

    /** @param Country[] $countries */
    private function renderProfilePage(
        User $user,
        array $countries,
        ?UserBilling $billing,
        ?UserShipping $shipping,
    ): Response {
        return $this->renderPage('features/user/profile/profile.html.twig', [
            'user'      => $user,
            'countries' => $countries,
            'billing'   => $billing,
            'shipping'  => $shipping,
        ]);
    }
}
