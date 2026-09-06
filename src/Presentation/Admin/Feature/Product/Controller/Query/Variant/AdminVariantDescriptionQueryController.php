<?php

declare(strict_types=1);

namespace App\Presentation\Admin\Feature\Product\Controller\Query\Variant;

use Symfony\Component\HttpFoundation\Response;

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Entity\Variant\ProductVariantDescription,
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Repository\Variant\ProductVariantRepositoryContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractFindQueryController,
    Shared\Responder\ExceptionResponder
};

final class AdminVariantDescriptionQueryController extends AbstractFindQueryController
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
            'features/admin/views/product/variant/description/index.html.twig',
            'admin_products_index',
            'variant',
        );
    }

    /**
     * @param int $id
     *
     * @return Response
    */
    public function create(int $id): Response
    {
        return $this->renderFindById(
            $id,
            'features/admin/views/product/variant/description/create.html.twig',
            'admin_products_index',
            'variant',
        );
    }

    /**
     * @param int $variantId
     * @param int $descriptionId
     *
     * @return Response
    */
    public function edit(int $variantId, int $descriptionId): Response
    {
        $variant = $this->getVariantOrThrow($variantId);
        $description = $this->findDescriptionOrThrow($variant, $descriptionId);

        return $this->renderPage(
            'features/admin/views/product/variant/description/edit.html.twig',
            [
                'variant'     => $variant,
                'description' => $description,
            ],
        );
    }

    /**
     * @return ProductVariantRepositoryContract
    */
    protected function getRepository(): ProductVariantRepositoryContract
    {
        return $this->variantRepository;
    }

    /**
     * @param int $variantId
     *
     * @return ProductVariant
    */
    private function getVariantOrThrow(int $variantId): ProductVariant
    {
        $variant = $this->variantRepository->findById($variantId);
        if (!$variant) {
            throw $this->createNotFoundException(sprintf('Variant with ID %d not found.', $variantId));
        }

        return $variant;
    }

    /**
     * @param ProductVariant $variant
     * @param int $descriptionId
     *
     * @return ProductVariantDescription
    */
    private function findDescriptionOrThrow(ProductVariant $variant, int $descriptionId): ProductVariantDescription
    {
        $description = $variant->getDescriptions()->filter(
            static fn(ProductVariantDescription $d): bool => $d->getId() === $descriptionId,
        )->first();

        if (!$description instanceof ProductVariantDescription) {
            throw $this->createNotFoundException(sprintf('Description with ID %d not found.', $descriptionId));
        }

        return $description;
    }
}
