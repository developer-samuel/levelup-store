<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractFindQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminVariantImageQueryController extends AbstractFindQueryController
{
    /**
     * @param ProductVariantRepositoryContract $variantRepository
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductVariantRepositoryContract $variantRepository,
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
     * @param int $id
     *
     * @return Response
    */
    public function index(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/product/variant/image/index.html.twig',
            'admin_products_index',
            'variant',
        );
    }

    /**
     * @return ProductVariantRepositoryContract
    */
    protected function getRepository(): ProductVariantRepositoryContract
    {
        return $this->variantRepository;
    }
}
