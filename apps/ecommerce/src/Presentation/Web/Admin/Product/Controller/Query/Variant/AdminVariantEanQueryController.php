<?php

declare(strict_types=1);

namespace App\Presentation\Web\Admin\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantEan,
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Shared\Responder\ExceptionResponder,
    Web\Abstract\Controller\Query\AbstractFindQueryController
};

final class AdminVariantEanQueryController extends AbstractFindQueryController
{
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

    public function index(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/product/variant/ean/index.html.twig',
            'admin_products_index',
            'variant',
        );
    }

    public function create(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/product/variant/ean/create.html.twig',
            'admin_products_index',
            'variant',
        );
    }

    public function edit(int $variantId, int $eanId): Response
    {
        $variant = $this->getVariantOrFail($variantId);
        $ean = $this->getEanOrFail($variant, $eanId);

        return $this->renderPage(
            'features/admin/views/product/variant/ean/edit.html.twig',
            [
                'variant' => $variant,
                'ean'     => $ean,
            ],
        );
    }

    protected function getRepository(): ProductVariantRepositoryContract
    {
        return $this->variantRepository;
    }

    private function getVariantOrFail(int $variantId): ProductVariant
    {
        $variant = $this->variantRepository->findById($variantId);
        if ($variant === null) {
            throw $this->createNotFoundException(sprintf('Variant with ID %d not found.', $variantId));
        }

        return $variant;
    }

    private function getEanOrFail(ProductVariant $variant, int $eanId): ProductVariantEan
    {
        $eans = $variant->getEans();

        $ean = $eans->filter(
            static fn(ProductVariantEan $d): bool => $d->getId() === $eanId,
        )->first();

        if (!$ean instanceof ProductVariantEan) {
            throw $this->createNotFoundException(sprintf('EAN with ID %d not found.', $eanId));
        }

        return $ean;
    }
}
