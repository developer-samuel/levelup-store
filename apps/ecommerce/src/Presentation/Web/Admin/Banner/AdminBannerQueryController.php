<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Banner;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\Segment\Banner\Enum\BannerType;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractQueryController
};

final class AdminBannerQueryController extends AbstractQueryController
{
    public function __construct(
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

    public function index(): Response
    {
        return $this->renderPage('features/admin/views/banner/index.html.twig');
    }

    public function create(): Response
    {
        return $this->renderPage('admin/pages/banners/create.html.twig', [
            'types'  => $this->getBannerTypeOptions(),
            'banner' => null,
        ]);
    }

    /**
     * @return list<array{
     *     id: string,
     *     name: string
     * }>
    */
    private function getBannerTypeOptions(): array
    {
        return array_values(array_map(
            static fn(BannerType $type): array => [
                'id'   => $type->value,
                'name' => $type->getLabel(),
            ],
            BannerType::cases(),
        ));
    }
}
