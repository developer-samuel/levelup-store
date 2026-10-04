<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Product\Controller\Query;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Repository\ProductRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractFindQueryController
};

final class AdminProductSubtypeQueryController extends AbstractFindQueryController
{
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

    public function index(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/product/subtype/index.html.twig',
            'admin_products_index',
            'product',
        );
    }

    protected function getRepository(): ProductRepositoryContract
    {
        return $this->productRepository;
    }
}
