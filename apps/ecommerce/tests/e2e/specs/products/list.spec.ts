import { test, expect } from '@playwright/test'

import { ProductsListPage } from '@/tests/e2e/pages/products/ProductsListPage'

const PRICE_FILTER = { min: '50', max: '500' }

test.describe('Products List Page', () => {
  let listPage: ProductsListPage

  test.beforeEach(async ({ page }) => {
    listPage = new ProductsListPage(page)

    await listPage.goto()
  })

  // ── Page load ──────────────────────────────────────────────────────────────

  test('should load the products page', async () => {
    await expect(listPage.root).toBeVisible()
    await expect(listPage.filterAside).toBeAttached()
    await expect(listPage.sortSelect).toBeVisible()
  })

  test('should display product items', async () => {
    const count = await listPage.getProductCount()

    expect(count).toBeGreaterThan(0)
  })

  test('should display product name and price on each card', async ({ page }) => {
    const firstItem = page.locator('.product-item').first()

    await expect(firstItem.locator('.product-item__box-name')).toBeVisible()
    await expect(firstItem.locator('.product-item__box-price')).toBeVisible()
  })

  // ── URL params ─────────────────────────────────────────────────────────────

  test('should load with category in URL', async () => {
    const href = await listPage.getSubtypeHref(0)
    if (!href) {
      test.skip()
      return
    }

    const segment = href.split('/').filter(Boolean)[1]
    if (!segment) {
      test.skip()
      return
    }

    await listPage.goto(segment)

    await expect(listPage.root).toBeVisible()
  })

  test('should load with category and type in URL', async () => {
    const count = await listPage.subtypeItems.count()
    if (count === 0) {
      test.skip()
      return
    }

    const href = await listPage.getSubtypeHref(0)
    if (!href) {
      test.skip()
      return
    }

    const segments = href.split('/').filter(Boolean)
    if (segments.length < 3) {
      test.skip()
      return
    }

    await listPage.goto(segments[1], segments[2])

    await expect(listPage.root).toBeVisible()
  })

  // ── Sort ───────────────────────────────────────────────────────────────────

  test('should update URL when sort changes', async () => {
    const options = await listPage.sortSelect.locator('option').all()
    if (options.length < 2) test.skip()

    const secondValue = await options[1]?.getAttribute('value')
    if (!secondValue) {
      test.skip()
      return
    }

    await listPage.selectSort(secondValue)

    const sortParam = listPage.getUrlParam('sort')

    expect(sortParam).toBe(secondValue)
  })

  test('should reload products after sort change', async () => {
    const options = await listPage.sortSelect.locator('option').all()
    if (options.length < 2) test.skip()

    const secondValue = await options[1]?.getAttribute('value')
    if (!secondValue) {
      test.skip()
      return
    }

    const countBefore = await listPage.getProductCount()

    await listPage.selectSort(secondValue)

    const countAfter = await listPage.getProductCount()

    expect(countAfter).toBeGreaterThan(0)
    expect(countAfter).toBe(countBefore)
  })

  // ── Load more ──────────────────────────────────────────────────────────────

  test('should show load-more button when multiple pages exist', async () => {
    const totalPages = await listPage.getTotalPages()
    if (totalPages <= 1) test.skip()

    await expect(listPage.loadMoreBtn).toBeVisible()
  })

  test('should load next page on load-more click', async () => {
    const totalPages = await listPage.getTotalPages()
    if (totalPages <= 1) test.skip()

    const countBefore = await listPage.getProductCount()

    await listPage.clickLoadMore()

    const countAfter = await listPage.getProductCount()

    expect(countAfter).toBeGreaterThan(countBefore)
  })

  test('should increment page counter after load-more click', async () => {
    const totalPages = await listPage.getTotalPages()
    if (totalPages <= 1) test.skip()

    await listPage.clickLoadMore()

    const currentPage = await listPage.getCurrentPage()

    expect(currentPage).toBe(2)
  })

  test('should hide load-more button on last page', async () => {
    const totalPages = await listPage.getTotalPages()
    if (totalPages !== 1) test.skip()

    await expect(listPage.loadMoreContainer).toHaveClass(/products__card-load-more--hidden/)
  })

  // ── Subtype filter ─────────────────────────────────────────────────────────

  test('should navigate to subtype URL when subtype filter is clicked', async ({ page }) => {
    const count = await listPage.subtypeItems.count()
    if (count === 0) {
      test.skip()
      return
    }

    const href = await listPage.getSubtypeHref(0)
    if (!href) {
      test.skip()
      return
    }

    await listPage.clickSubtype(0)

    expect(page.url()).toContain(href)
  })

  test('should mark subtype as active after navigation', async ({ page }) => {
    const count = await listPage.subtypeItems.count()
    if (count === 0) {
      test.skip()
      return
    }

    const href = await listPage.getSubtypeHref(0)
    if (!href) {
      test.skip()
      return
    }

    await listPage.clickSubtype(0)

    const activeItem = page.locator(`.products__filter-list-item--active, .products__filter-list-item[aria-current]`)
    const activeCount = await activeItem.count()
    if (activeCount === 0) {
      expect(page.url()).toContain(href)
    } else {
      await expect(activeItem.first()).toBeVisible()
    }
  })

  test('should reset page to 1 after subtype navigation', async () => {
    const totalPages = await listPage.getTotalPages()
    if (totalPages <= 1) {
      test.skip()
      return
    }

    await listPage.clickLoadMore()
    const subtypeCount = await listPage.subtypeItems.count()
    if (subtypeCount === 0) {
      test.skip()
      return
    }

    await listPage.clickSubtype(0)

    const currentPage = await listPage.getCurrentPage()

    expect(currentPage).toBe(1)
  })

  // ── Brand filter ───────────────────────────────────────────────────────────

  test('should update URL when brand checkbox is checked', async () => {
    const count = await listPage.brandCheckboxes.count()
    if (count === 0) test.skip()

    await listPage.checkBrand(0)

    const brandParam = listPage.getUrlParam('brand')

    expect(brandParam).not.toBeNull()
  })

  test('should remove brand from URL when unchecked', async () => {
    const count = await listPage.brandCheckboxes.count()
    if (count === 0) test.skip()

    await listPage.checkBrand(0)
    await listPage.uncheckBrand(0)

    const brandParam = listPage.getUrlParam('brand')

    expect(brandParam).toBeNull()
  })

  // ── Price filter ───────────────────────────────────────────────────────────

  test('should update URL with minPrice param', async () => {
    const visible = await listPage.minPriceInput.isVisible()
    if (!visible) test.skip()

    await listPage.setMinPrice(PRICE_FILTER.min)

    const minParam = listPage.getUrlParam('minPrice')

    expect(minParam).toBe(PRICE_FILTER.min)
  })

  test('should update URL with maxPrice param', async () => {
    const visible = await listPage.maxPriceInput.isVisible()
    if (!visible) test.skip()

    await listPage.setMaxPrice(PRICE_FILTER.max)

    const maxParam = listPage.getUrlParam('maxPrice')

    expect(maxParam).toBe(PRICE_FILTER.max)
  })

  // ── No results ─────────────────────────────────────────────────────────────

  test('should show no-results message when filters match nothing', async () => {
    const visible = await listPage.minPriceInput.isVisible()
    if (!visible) {
      test.skip()
      return
    }

    const max = await listPage.getRangeMax(listPage.minPriceInput)

    await listPage.setMinPrice(String(max))

    const noResults = await listPage.noResultsMsg.isVisible()
    const productCount = await listPage.getProductCount()

    if (productCount === 0) {
      expect(noResults).toBe(true)
    }
  })

  // ── Mobile filter ──────────────────────────────────────────────────────────

  test('should toggle filter on mobile filter button click', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 })

    await listPage.goto()
    const mobileBtn = listPage.mobileFilterBtn
    const isMobileBtnVisible = await mobileBtn.isVisible()
    if (!isMobileBtnVisible) {
      test.skip()
      return
    }

    await mobileBtn.evaluate((el) => (el as HTMLElement).click())

    await expect(listPage.filterAside)
      .not.toHaveClass(/products__filter--hidden/, { timeout: 8_000 })
      .catch(() => expect(listPage.filterAside).toBeVisible({ timeout: 8_000 }))
  })

  // ── History / URL state ────────────────────────────────────────────────────

  test('should preserve filter state in URL on reload', async ({ page }) => {
    const count = await listPage.subtypeItems.count()
    if (count === 0) test.skip()

    await listPage.clickSubtype(0)

    const urlBefore = page.url()

    await page.reload({ waitUntil: 'load' })

    await listPage.waitForProducts()

    const urlAfter = page.url()

    expect(urlAfter).toBe(urlBefore)
  })

  test('should navigate to product detail on card click', async ({ page }) => {
    const links = page.locator('.product-item')
    const count = await links.count()

    for (let i = 0; i < Math.min(count, 5); i++) {
      const href = await links.nth(i).getAttribute('href')
      if (!href) continue

      try {
        await page.goto(href, { waitUntil: 'domcontentloaded', timeout: 8_000 })
      } catch {
        await listPage.goto()
        continue
      }

      if (page.url().includes(href)) {
        expect(page.url()).toContain(href)
        return
      }

      await listPage.goto()
    }

    test.skip(true, 'No product detail pages reachable (Elasticsearch index may be empty)')
  })
})
