<?php

declare(strict_types=1);

namespace App\Presentation\Segment\Product\Controller\Query;

use Symfony\{
    Component\HttpFoundation\Request,
    Component\HttpFoundation\Response
};

use Kit\{
    Utils\Shared\Normalizer\StringNormalizer,
    Utils\Shared\Sanitizer\DataSanitizer
};

use App\Core\Domain\{
    Segment\Product\Entity\Variant\ProductVariant,
    Segment\Product\Enum\ProductFilterParam,
    Segment\Product\Enum\ProductSortOption,
    Segment\Product\ValueObject\ProductFilterObject,
    Segment\Product\ValueObject\ProductListObject
};

use App\Core\Ports\{
    Security\Provider\SecurityProviderContract,
    Segment\Product\Handler\Query\ProductDetailQueryHandlerContract,
    Segment\Product\Handler\Query\ProductQueryHandlerContract,
    Segment\Product\Renderer\ProductRendererContract,
    Shared\Logging\AppLoggerContract
};

use App\Presentation\{
    Abstract\Controller\Query\AbstractQueryController,
    Shared\Responder\ExceptionResponder
};

class ProductQueryController extends AbstractQueryController
{
    /**
     * @param ProductQueryHandlerContract $productQueryHandler
     * @param ProductDetailQueryHandlerContract $productDetailQueryHandler
     * @param ProductRendererContract $productRenderer
     * @param SecurityProviderContract $securityProvider
     * @param ExceptionResponder $exceptionResponder
     * @param AppLoggerContract $logger
    */
    public function __construct(
        private readonly ProductQueryHandlerContract $productQueryHandler,
        private readonly ProductDetailQueryHandlerContract $productDetailQueryHandler,
        private readonly ProductRendererContract $productRenderer,
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
     * @param Request $request
     * @param ?string $category
     * @param ?string $type
     *
     * @return Response
    */
    public function index(Request $request, ?string $category = null, ?string $type = null): Response
    {
        $query = $request->query->all();
        $brands = $this->normalizeValues($request->query->get('brand', ''));
        $subtypes = $this->normalizeValues($request->query->get('subtype', ''));

        $isDiscountPath = str_contains($request->getPathInfo(), '/discounts');

        $filter = $this->createFilter($query, $category, $type, $brands, $subtypes, $isDiscountPath);
        $currentPage = (int) ($query['page'] ?? 1);
        $sort = $this->resolveSort($query);

        $result = $this->productQueryHandler->handle($filter, $currentPage, $sort);

        $data = $this->enrichData($result, $filter, $brands, $subtypes);

        if ($request->isXmlHttpRequest()) {
            $list = $this->createProductList($data);

            return $this->productRenderer->renderProductsList($list);
        }

        $redirectUrl = $this->determineRedirectUrl($request, $data, $query);
        if ($redirectUrl !== null) {
            return $this->redirect($redirectUrl);
        }

        return $this->productRenderer->renderProducts($data);
    }

    /**
     * @param string $url
     *
     * @return Response
    */
    public function show(string $url): Response
    {
        $result = $this->productDetailQueryHandler->handle($url);
        if (!$result) {
            return $this->redirectToRoute('products_index');
        }

        return $this->productRenderer->renderProductDetail($result);
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return ProductListObject
    */
    private function createProductList(array $data): ProductListObject
    {
        /** @var array<int, ProductVariant> $variants */
        $variants = $data['variants'] ?? [];

        return new ProductListObject(
            variants: $variants,
            maxPages: DataSanitizer::sanitizeInt($data['maxPages'] ?? null) ?? 0,
            currentPage: DataSanitizer::sanitizeInt($data['currentPage'] ?? null) ?? 1,
            sort: DataSanitizer::sanitizeString($data['sort'] ?? null),
            totalCount: DataSanitizer::sanitizeInt($data['totalCount'] ?? null) ?? 0,
            showLoadMore: DataSanitizer::sanitizeBoolean($data['showLoadMore'] ?? false),
        );
    }

    /**
     * @param mixed $raw
     *
     * @return string[]
    */
    private function normalizeValues(mixed $raw): array
    {
        return $this->mapNormalizedItems(
            $this->resolveRawItems($raw),
        );
    }

    /**
     * @param mixed $raw
     * 
     * @return string[]
    */
    private function resolveRawItems(mixed $raw): array
    {
        $sanitizedString = DataSanitizer::sanitizeString($raw);

        return array_values(array_filter(
            DataSanitizer::sanitizeStringArray(explode(',', $sanitizedString)),
        ));
    }

    /**
     * @param string[] $items
     *
     * @return string[]
    */
    private function mapNormalizedItems(array $items): array
    {
        return array_map(
            fn($item): string => $this->normalizeItem(
                DataSanitizer::sanitizeString($item),
            ),
            $items,
        );
    }

    /**
     * @param string $item
     *
     * @return string
    */
    private function normalizeItem(string $item): string
    {
        return StringNormalizer::toLowerCase(
            StringNormalizer::replaceSpacesWithDash($item),
        );
    }

    /**
     * @param array<string, mixed> $query
     * @param string|null $category
     * @param string|null $type
     * @param string[] $brands
     * @param string[] $subtypes
     *
     * @return ProductFilterObject
    */
    private function createFilter(
        array $query,
        ?string $category,
        ?string $type,
        array $brands,
        array $subtypes,
        bool $isDiscountPath,
    ): ProductFilterObject {
        return new ProductFilterObject(
            isDiscountRoute: $isDiscountPath,
            subtypes: $subtypes,
            brands: $brands,
            category: $category ?? null,
            type: $type ?? null,
            minPrice: $this->getPriceFromQuery($query, 'minPrice'),
            maxPrice: $this->getPriceFromQuery($query, 'maxPrice'),
        );
    }

    /**
     * @param array<string, mixed> $query
     * @param string $priceKey
     *
     * @return float|null
    */
    private function getPriceFromQuery(array $query, string $priceKey): ?float
    {
        $value = $query[$priceKey] ?? null;

        return DataSanitizer::sanitizeFloat($value);
    }

    /**
     * @param array<string, mixed> $data
     * @param ProductFilterObject $filter
     * @param string[] $brands
     * @param string[] $subtypes
     *
     * @return array<string, mixed>
    */
    private function enrichData(
        array $data,
        ProductFilterObject $filter,
        array $brands,
        array $subtypes,
    ): array {
        $data['minPriceActive'] = $filter->minPrice ?? 0;
        $data['maxPriceActive'] = $filter->maxPrice ?? 0;
        $data['brandsSelected'] = $brands;
        $data['subtypesSelected'] = $subtypes;

        return $data;
    }

    /**
     * @param array<string, mixed> $query
     *
     * @return ProductSortOption
    */
    private function resolveSort(array $query): ProductSortOption
    {
        return isset($query['sort']) && is_string($query['sort']) ?
            ProductSortOption::from($query['sort']) :
            ProductSortOption::TOP_RATED;
    }

    /**
     * @param Request $request
     * @param array<string, mixed> $data
     * @param array<string, mixed> $params
     *
     * @return string|null
     */
    private function determineRedirectUrl(Request $request, array $data, array $params): ?string
    {
        $currentPage = $this->getCurrentPageFromData($data);

        if ($currentPage > 1 && empty($data['products'] ?? [])) {
            unset($params['page']);

            return $this->buildRedirectUri($request, $params);
        }

        $clearedParams = $this->clearFilterParams($params);
        if (is_array($clearedParams)) {
            return $this->buildRedirectUri($request, $clearedParams);
        }

        return null;
    }

    /**
     * @param array<string, mixed> $data
     *
     * @return int
    */
    private function getCurrentPageFromData(array $data): int
    {
        $pagination = $data['pagination'] ?? null;

        if (is_array($pagination) && is_scalar($pagination['currentPage'] ?? null)) {
            return (int) $pagination['currentPage'];
        }

        return 1;
    }

    /**
     * @param array<string, mixed> $params
     *
     * @return array<string, mixed>|null
    */
    private function clearFilterParams(array $params): ?array
    {
        $filterParams = ProductFilterParam::values();
        if (!array_intersect_key(array_flip($filterParams), $params)) {
            return null;
        }

        foreach ($filterParams as $param) {
            unset($params[$param]);
        }

        return $params;
    }

    /**
     * @param Request $request
     * @param array<string, mixed> $params
     *
     * @return string
    */
    private function buildRedirectUri(Request $request, array $params): string
    {
        return $request->getUriForPath($request->getPathInfo())
            . (empty($params) ? '' : '?' . http_build_query($params));
    }
}
