vi.mock('@/ts/features/products/list/_utils/pagination', () => ({
  parseInitialPage: vi.fn(),
  updateDataset: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_ui/loadMore', () => ({
  checkLoadMoreVisibility: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_listeners/filterListener', () => ({
  attachFilterListener: vi.fn(),
}))

vi.mock('@/ts/features/products/list/_interactions/mobileFilter', () => ({
  setupMobileFilter: vi.fn(),
}))

import { parseInitialPage, updateDataset } from '@/ts/features/products/list/_utils/pagination'
import { checkLoadMoreVisibility } from '@/ts/features/products/list/_ui/loadMore'
import { attachFilterListener } from '@/ts/features/products/list/_listeners/filterListener'
import { setupMobileFilter } from '@/ts/features/products/list/_interactions/mobileFilter'
import ProductList from '@/ts/features/products/list/_components/ProductList'

const mockedParseInitialPage = vi.mocked(parseInitialPage)
const mockedUpdateDataset = vi.mocked(updateDataset)
const mockedCheckLoadMoreVisibility = vi.mocked(checkLoadMoreVisibility)
const mockedAttachFilterListener = vi.mocked(attachFilterListener)
const mockedSetupMobileFilter = vi.mocked(setupMobileFilter)

function setupWrapper(id: string, currentPage = '1', totalPage = '5'): HTMLElement {
  const el = document.createElement('div')
  el.id = id
  el.dataset.currentPage = currentPage
  el.dataset.totalPage = totalPage
  document.body.appendChild(el)
  return el
}

describe('ProductList', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
    mockedParseInitialPage.mockReturnValueOnce(1).mockReturnValueOnce(5)
  })

  it('should throw when wrapper element does not exist', () => {
    expect(() => new ProductList('nonexistent')).toThrow('ProductList: #nonexistent not found')
  })

  it('should set productsWrapper to the found element', () => {
    const el = setupWrapper('products')
    const instance = new ProductList('products')
    expect(instance.productsWrapper).toBe(el)
  })

  it('should set page from parseInitialPage with currentPage key', () => {
    const el = setupWrapper('products')
    mockedParseInitialPage.mockReset()
    mockedParseInitialPage.mockReturnValueOnce(3).mockReturnValueOnce(10)

    const instance = new ProductList('products')

    expect(mockedParseInitialPage).toHaveBeenNthCalledWith(1, el, undefined, 'currentPage')
    expect(instance.page).toBe(3)
  })

  it('should set maxPages from parseInitialPage with totalPage key', () => {
    const el = setupWrapper('products')
    mockedParseInitialPage.mockReset()
    mockedParseInitialPage.mockReturnValueOnce(1).mockReturnValueOnce(8)

    const instance = new ProductList('products')

    expect(mockedParseInitialPage).toHaveBeenNthCalledWith(2, el, undefined, 'totalPage')
    expect(instance.maxPages).toBe(8)
  })

  it('should set isLoading to false', () => {
    setupWrapper('products')
    const instance = new ProductList('products')
    expect(instance.isLoading).toBe(false)
  })

  it('should call attachFilterListener with the instance', () => {
    setupWrapper('products')
    const instance = new ProductList('products')
    expect(mockedAttachFilterListener).toHaveBeenCalledWith(instance)
  })

  it('should call updateDataset with wrapper, page and maxPages', () => {
    const el = setupWrapper('products')
    mockedParseInitialPage.mockReset()
    mockedParseInitialPage.mockReturnValueOnce(2).mockReturnValueOnce(6)

    new ProductList('products')

    expect(mockedUpdateDataset).toHaveBeenCalledWith(el, 2, 6)
  })

  it('should call checkLoadMoreVisibility with page, maxPages and wrapper', () => {
    const el = setupWrapper('products')
    mockedParseInitialPage.mockReset()
    mockedParseInitialPage.mockReturnValueOnce(2).mockReturnValueOnce(6)

    new ProductList('products')

    expect(mockedCheckLoadMoreVisibility).toHaveBeenCalledWith(2, 6, el)
  })

  it('should call setupMobileFilter', () => {
    setupWrapper('products')
    new ProductList('products')
    expect(mockedSetupMobileFilter).toHaveBeenCalledTimes(1)
  })
})
