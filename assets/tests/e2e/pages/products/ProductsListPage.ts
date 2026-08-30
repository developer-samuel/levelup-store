import type { Locator } from '@playwright/test'
import { expect } from '@playwright/test'

import type { StringRecord } from '@/ts/shared/types'

import { getNumericAttribute } from '@/tests/e2e/utils/attributes'

import { BasePage } from '@/tests/e2e/pages/abstracts/BasePage'

export class ProductsListPage extends BasePage {
  // Root
  readonly root: Locator
  readonly productsWrapper: Locator

  // Filter
  readonly filterAside: Locator
  readonly filterCloseBtn: Locator
  readonly mobileFilterBtn: Locator
  readonly subtypeItems: Locator
  readonly brandCheckboxes: Locator
  readonly minPriceInput: Locator
  readonly maxPriceInput: Locator

  // Products
  readonly productItems: Locator
  readonly sortSelect: Locator
  readonly loadMoreBtn: Locator
  readonly loadMoreContainer: Locator
  readonly noResultsMsg: Locator

  constructor(page: ConstructorParameters<typeof BasePage>[0]) {
    super(page)

    this.root = page.locator('.products')
    this.productsWrapper = page.locator('#products-wrapper')

    this.filterAside = page.locator('.products__filter')
    this.filterCloseBtn = page.locator('.products__filter-close')
    this.mobileFilterBtn = page.locator('.products__filter-mobile')
    this.subtypeItems = page.locator('.products__filter-list-item')
    this.brandCheckboxes = page.locator('.products__filter-checkbox-list-input')
    this.minPriceInput = page.locator('#minPrice')
    this.maxPriceInput = page.locator('#maxPrice')

    this.productItems = page.locator('.product-item')
    this.sortSelect = page.locator('#sort-by')
    this.loadMoreBtn = page.locator('#load-more')
    this.loadMoreContainer = page.locator('.products__card-load-more')
    this.noResultsMsg = page.locator('.products__card-no-results')
  }

  async goto(category?: string, type?: string): Promise<void> {
    const path = this.buildPath(category, type)

    await super.goto(path)

    await this.waitForProducts()
  }

  async gotoWithParams(category?: string, type?: string, params: StringRecord = {}): Promise<void> {
    const path = this.buildPath(category, type)
    const query = new URLSearchParams(params).toString()

    await super.goto(query ? `${path}?${query}` : path)

    await this.waitForProducts()
  }

  async waitForProducts(): Promise<void> {
    await expect(this.root).toBeVisible({ timeout: 15_000 })
  }

  async waitForNetworkIdle(): Promise<void> {
    await expect(this.productItems.first()).toBeAttached()
  }

  async getProductCount(): Promise<number> {
    return this.productItems.count()
  }

  async getCurrentPage(): Promise<number> {
    return getNumericAttribute(this.productsWrapper, 'data-current-page')
  }

  async getTotalPages(): Promise<number> {
    return getNumericAttribute(this.productsWrapper, 'data-total-page')
  }

  async selectSort(value: string): Promise<void> {
    await this.sortSelect.evaluate((el, val) => {
      const select = el as HTMLSelectElement
      select.value = val
      select.dispatchEvent(new Event('change', { bubbles: true }))
    }, value)

    await this.page.waitForFunction((v) => new URL(window.location.href).searchParams.get('sort') === v, value, {
      timeout: 10_000,
    })
  }

  async clickLoadMore(): Promise<void> {
    const countBefore = await this.productItems.count()

    await this.loadMoreBtn.scrollIntoViewIfNeeded()

    await Promise.all([
      this.page.waitForResponse((resp) => resp.url().includes('/products') && resp.status() === 200, {
        timeout: 15_000,
      }),
      this.loadMoreBtn.click({ force: true }),
    ])

    await this.page.waitForFunction((n) => document.querySelectorAll('.product-item').length > n, countBefore, {
      timeout: 10_000,
    })
  }

  async openFilterIfHidden(): Promise<void> {
    const isVisible = await this.filterAside.isVisible().catch(() => false)

    if (!isVisible) {
      const isMobileBtnVisible = await this.mobileFilterBtn.isVisible().catch(() => false)

      if (isMobileBtnVisible) {
        await this.mobileFilterBtn.click()
      } else {
        await this.mobileFilterBtn.click({ force: true }).catch(() => {})
      }

      await this.filterAside.waitFor({ state: 'visible', timeout: 8_000 }).catch(() => {})
    }
  }

  async clickSubtype(index: number): Promise<void> {
    const href = await this.subtypeItems.nth(index).getAttribute('href')
    if (!href) return

    await this.page.goto(href, { waitUntil: 'domcontentloaded' })

    await this.waitForNetworkIdle()
  }

  async getSubtypeHref(index: number): Promise<string | null> {
    return this.subtypeItems.nth(index).getAttribute('href')
  }

  async checkBrand(index: number): Promise<void> {
    await this.openFilterIfHidden()

    await this.brandCheckboxes.nth(index).evaluate((el) => {
      const input = el as HTMLInputElement
      input.checked = true
      input.dispatchEvent(new Event('change', { bubbles: true }))
    })

    await this.page.waitForFunction(() => new URL(window.location.href).searchParams.has('brand'), { timeout: 10_000 })
  }

  async uncheckBrand(index: number): Promise<void> {
    await this.openFilterIfHidden()

    await this.brandCheckboxes.nth(index).evaluate((el) => {
      const input = el as HTMLInputElement
      input.checked = false
      input.dispatchEvent(new Event('change', { bubbles: true }))
    })

    await this.page.waitForFunction(() => !new URL(window.location.href).searchParams.has('brand'), { timeout: 10_000 })
  }

  async setMinPrice(value: string): Promise<void> {
    await this.openFilterIfHidden()

    await this.minPriceInput.evaluate((el, val) => {
      ;(el as HTMLInputElement).value = val
    }, value)

    await this.minPriceInput.dispatchEvent('change')

    await this.page.waitForFunction((v) => new URL(window.location.href).searchParams.get('minPrice') === v, value, {
      timeout: 10_000,
    })
  }

  async setMaxPrice(value: string): Promise<void> {
    await this.openFilterIfHidden()

    await this.maxPriceInput.evaluate((el, val) => {
      ;(el as HTMLInputElement).value = val
    }, value)

    await this.maxPriceInput.dispatchEvent('change')

    await this.page.waitForFunction((v) => new URL(window.location.href).searchParams.get('maxPrice') === v, value, {
      timeout: 10_000,
    })
  }

  async getRangeMax(locator: Locator): Promise<number> {
    return getNumericAttribute(locator, 'max', 0)
  }

  getCurrentUrl(): URL {
    return new URL(this.page.url())
  }

  getUrlParam(param: string): string | null {
    const url = this.getCurrentUrl()

    return url.searchParams.get(param)
  }

  private buildPath(category?: string, type?: string): string {
    if (category && type) return `/products/${category}/${type}`
    if (category) return `/products/${category}`

    return '/products'
  }
}
