vi.mock('@/ts/features/products/list/_filters/brandFilter', () => ({
  setupBrandFilter: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_filters/priceFilter', () => ({
  setupPriceFilter: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_filters/subtypeFilter', () => ({
  setupSubtypeFilter: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_listeners/sortListener', () => ({
  attachSortListener: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_listeners/loadMoreListener', () => ({
  attachLoadMoreListener: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_ui/reset', () => ({
  resetFilterUI: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_interactions/updateProducts', () => ({
  updateProducts: vi.fn(),
}))

vi.mock('@/ts/shared/utils/query', () => ({
  parseQueryParams: vi.fn(),
}))

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { setupBrandFilter } from '@/ts/features/products/list/_filters/brandFilter'
import { setupPriceFilter } from '@/ts/features/products/list/_filters/priceFilter'
import { setupSubtypeFilter } from '@/ts/features/products/list/_filters/subtypeFilter'
import { attachFilterListener } from '@/ts/features/products/list/_listeners/filterListener'
import { attachSortListener } from '@/ts/features/products/list/_listeners/sortListener'
import { attachLoadMoreListener } from '@/ts/features/products/list/_listeners/loadMoreListener'
import { resetFilterUI } from '@/ts/features/products/list/_ui/reset'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'
import { parseQueryParams } from '@/ts/shared/utils/query'

const mockedSetupBrandFilter = vi.mocked(setupBrandFilter)
const mockedSetupPriceFilter = vi.mocked(setupPriceFilter)
const mockedSetupSubtypeFilter = vi.mocked(setupSubtypeFilter)
const mockedAttachLoadMoreListener = vi.mocked(attachLoadMoreListener)
const mockedAttachSortListener = vi.mocked(attachSortListener)
const mockedResetFilterUI = vi.mocked(resetFilterUI)
const mockedUpdateProducts = vi.mocked(updateProducts)
const mockedParseQueryParams = vi.mocked(parseQueryParams)

const ctx = { page: 1 } as ProductListInstance

describe('attachFilterListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
    mockedUpdateProducts.mockResolvedValue({})
    mockedParseQueryParams.mockReturnValue({})
  })

  it('should call setupSubtypeFilter', () => {
    attachFilterListener(ctx)
    expect(mockedSetupSubtypeFilter).toHaveBeenCalledWith(ctx)
  })

  it('should call setupBrandFilter', () => {
    attachFilterListener(ctx)
    expect(mockedSetupBrandFilter).toHaveBeenCalledWith(ctx)
  })

  it('should call setupPriceFilter', () => {
    attachFilterListener(ctx)
    expect(mockedSetupPriceFilter).toHaveBeenCalledWith(ctx)
  })

  it('should call attachSortListener with ctx and sort-by element', () => {
    const sortSelect = document.createElement('select')
    sortSelect.id = 'sort-by'
    document.body.appendChild(sortSelect)

    attachFilterListener(ctx)

    expect(mockedAttachSortListener).toHaveBeenCalledWith(ctx, sortSelect)
  })

  it('should call attachSortListener with null when sort-by element does not exist', () => {
    attachFilterListener(ctx)
    expect(mockedAttachSortListener).toHaveBeenCalledWith(ctx, null)
  })

  it('should call attachLoadMoreListener', () => {
    attachFilterListener(ctx)
    expect(mockedAttachLoadMoreListener).toHaveBeenCalledWith(ctx)
  })

  it('should not call resetFilterUI when reset button is absent', () => {
    attachFilterListener(ctx)
    expect(mockedResetFilterUI).not.toHaveBeenCalled()
  })

  it('should not reset when no filter params and page is 1', () => {
    mockedParseQueryParams.mockReturnValue({ category: 'shoes', page: '1' })
    const btn = document.createElement('button')
    btn.id = 'filter-reset'
    document.body.appendChild(btn)

    attachFilterListener(ctx)
    btn.click()

    expect(mockedResetFilterUI).not.toHaveBeenCalled()
    expect(mockedUpdateProducts).not.toHaveBeenCalled()
  })

  it('should reset when filter params are present', () => {
    mockedParseQueryParams.mockReturnValue({ category: 'shoes', brand: 'nike' })
    const btn = document.createElement('button')
    btn.id = 'filter-reset'
    document.body.appendChild(btn)
    const maxPriceEl = document.createElement('input') as HTMLInputElement
    maxPriceEl.id = 'maxPrice'
    maxPriceEl.max = '500'
    document.body.appendChild(maxPriceEl)

    attachFilterListener(ctx)
    btn.click()

    expect(mockedResetFilterUI).toHaveBeenCalledWith('500')
    expect(mockedUpdateProducts).toHaveBeenCalledWith(expect.objectContaining({ page: 1 }), ctx)
  })

  it('should reset when page is beyond first page', () => {
    mockedParseQueryParams.mockReturnValue({ page: '3' })
    const btn = document.createElement('button')
    btn.id = 'filter-reset'
    document.body.appendChild(btn)

    attachFilterListener(ctx)
    btn.click()

    expect(mockedResetFilterUI).toHaveBeenCalled()
    expect(mockedUpdateProducts).toHaveBeenCalled()
  })

  it('should preserve sort param in updateProducts call when sort is defined', () => {
    mockedParseQueryParams.mockReturnValue({ sort: 'price-asc', brand: 'nike' })
    const btn = document.createElement('button')
    btn.id = 'filter-reset'
    document.body.appendChild(btn)

    attachFilterListener(ctx)
    btn.click()

    expect(mockedUpdateProducts).toHaveBeenCalledWith(
      expect.objectContaining({ sort: 'price-asc', page: 1 }),
      ctx,
    )
  })
})
