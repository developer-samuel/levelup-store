<?php

declare(strict_types=1);

namespace App\Presentation\Segment\User\Controller\Query;

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
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

use App\Shared\Responder\ErrorResponder;

class ProfileQueryController extends AbstractQueryController
{
    /**
     * @param CountryCacheQueryContract $countryCacheQuery
     * @param ErrorResponder $errorResponder
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @return Response
    */
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

    /**
     * @param User $user
     * @param Country[] $countries
     * @param UserBilling|null $billing
     * @param UserShipping|null $shipping
     *
     * @return Response
    */
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
