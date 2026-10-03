<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Brand\Controller;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Brand\BrandRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractFindQueryController
};

final class AdminBrandQueryController extends AbstractFindQueryController
{
    public function __construct(
        private readonly BrandRepositoryContract $brandRepository,
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
        return $this->renderPage('features/admin/views/brand/index.html.twig');
    }

    public function create(): Response
    {
        return $this->renderPage('features/admin/views/brand/create.html.twig');
    }

    public function edit(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/brand/edit.html.twig',
            'admin_brands_index',
            'brand',
        );
    }

    protected function getRepository(): BrandRepositoryContract
    {
        return $this->brandRepository;
    }
}
