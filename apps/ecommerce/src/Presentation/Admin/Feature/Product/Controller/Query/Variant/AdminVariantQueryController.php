<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Repository\ProductRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractFindQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminVariantQueryController extends AbstractFindQueryController
{
    /**
     * @param ProductRepositoryContract $productRepository
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductRepositoryContract $productRepository,
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
            'features/admin/views/product/variant/index.html.twig',
            'admin_products_index',
            'product',
        );
    }

    /**
     * @return ProductRepositoryContract
    */
    protected function getRepository(): ProductRepositoryContract
    {
        return $this->productRepository;
    }
}
