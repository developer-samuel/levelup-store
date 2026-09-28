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

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { setupBrandFilter } from '@/ts/features/products/list/_filters/brandFilter'
import { setupPriceFilter } from '@/ts/features/products/list/_filters/priceFilter'
import { setupSubtypeFilter } from '@/ts/features/products/list/_filters/subtypeFilter'
import { attachFilterListener } from '@/ts/features/products/list/_listeners/filterListener'
import { attachSortListener } from '@/ts/features/products/list/_listeners/sortListener'
import { attachLoadMoreListener } from '@/ts/features/products/list/_listeners/loadMoreListener'

const mockedSetupBrandFilter = vi.mocked(setupBrandFilter)
const mockedSetupPriceFilter = vi.mocked(setupPriceFilter)
const mockedSetupSubtypeFilter = vi.mocked(setupSubtypeFilter)
const mockedAttachLoadMoreListener = vi.mocked(attachLoadMoreListener)
const mockedAttachSortListener = vi.mocked(attachSortListener)

const ctx = {} as ProductListInstance

describe('attachFilterListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
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
})
