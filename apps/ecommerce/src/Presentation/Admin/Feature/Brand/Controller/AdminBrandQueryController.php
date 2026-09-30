<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Brand\Controller;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Brand\BrandRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractFindQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminBrandQueryController extends AbstractFindQueryController
{
    /**
     * @param BrandRepositoryContract $brandRepository
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
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

    /**
     * @return Response
    */
    public function index(): Response
    {
        return $this->renderPage('features/admin/views/brand/index.html.twig');
    }

    /**
     * @return Response
    */
    public function create(): Response
    {
        return $this->renderPage('features/admin/views/brand/create.html.twig');
    }

    /**
     * @param int $id
     *
     * @return Response
    */
    public function edit(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/brand/edit.html.twig',
            'admin_brands_index',
            'brand',
        );
    }

    /**
     * @return BrandRepositoryContract
    */
    protected function getRepository(): BrandRepositoryContract
    {
        return $this->brandRepository;
    }
}
