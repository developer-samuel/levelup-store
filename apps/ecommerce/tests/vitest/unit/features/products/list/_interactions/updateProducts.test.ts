import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'
import { mockUtilsLogger, mockUtilsScroll } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'

mockAxios()
mockUtilsLogger()
mockUtilsScroll()
mockNotyfAlert()

vi.mock('@/ts/shared/events/loading', () => ({
  dispatchLoadingShow: vi.fn(),
  dispatchLoadingHide: vi.fn(),
}))
vi.mock('@/ts/features/products/list/_services/productsService', () => ({
  fetchProductHtml: vi.fn(),
}))
vi.mock('@/ts/features/products/list/_ui/loadMore', () => ({
  normalizeLoadMore: vi.fn(),
}))
vi.mock('@/ts/features/products/list/_ui/updater', () => ({
  updateProductList: vi.fn(),
}))
vi.mock('@/ts/features/products/list/_ui/wrapper', () => ({
  parseProductWrapper: vi.fn(),
}))
vi.mock('@/ts/features/products/list/_state/pagination', () => ({
  updatePaginationState: vi.fn(),
}))
vi.mock('@/ts/shared/utils/query', () => ({
  buildQueryString: vi.fn(),
}))

import { makeProductListCtx, makeProductListWrapper } from '@/tests/_support/fakers/features/products/list.fakers'
import { fetchProductHtml } from '@/ts/features/products/list/_services/productsService'
import { parseProductWrapper } from '@/ts/features/products/list/_ui/wrapper'
import { scrollToTop } from '@/ts/shared/utils/scroll'
import { logDevError } from '@/ts/shared/utils/logger'
import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'
import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'
import { buildQueryString } from '@/ts/shared/utils/query'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

const mockedFetchProductHtml = vi.mocked(fetchProductHtml)
const mockedParseProductWrapper = vi.mocked(parseProductWrapper)
const mockedScrollToTop = vi.mocked(scrollToTop)
const mockedLogDevError = vi.mocked(logDevError)
const mockedDispatchLoadingShow = vi.mocked(dispatchLoadingShow)
const mockedDispatchLoadingHide = vi.mocked(dispatchLoadingHide)
const mockedNotyfError = vi.mocked(NotyfAlert.error)
const mockedBuildQueryString = vi.mocked(buildQueryString)

describe('updateProducts()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedBuildQueryString.mockReturnValue('page=1')
    mockedFetchProductHtml.mockResolvedValue('<div class="products__wrapper"></div>')
    mockedParseProductWrapper.mockReturnValue(makeProductListWrapper())

    // mock history.pushState
    vi.spyOn(window.history, 'pushState').mockImplementation(() => {})
  })

  it('should dispatch loadingShow and loadingHide', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    await updateProducts({ page: 1 }, ctx)
    expect(mockedDispatchLoadingShow).toHaveBeenCalled()
    expect(mockedDispatchLoadingHide).toHaveBeenCalled()
  })

  it('should call fetchProductHtml', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    await updateProducts({ page: 1 }, ctx)
    expect(mockedFetchProductHtml).toHaveBeenCalled()
  })

  it('should scroll to top when page is 1', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    await updateProducts({ page: 1 }, ctx)
    expect(mockedScrollToTop).toHaveBeenCalled()
  })

  it('should not scroll to top when page is not 1', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    await updateProducts({ page: 2 }, ctx)
    expect(mockedScrollToTop).not.toHaveBeenCalled()
  })

  it('should return currentPage and maxPages from ctx', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper(), page: 2, maxPages: 8 })
    const result = await updateProducts({ page: 2 }, ctx)
    expect(result).toMatchObject({ currentPage: 2, maxPages: 8 })
  })

  it('should log error and show notyf on fetch failure', async () => {
    mockedFetchProductHtml.mockRejectedValueOnce(new Error('Network'))
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    const result = await updateProducts({ page: 1 }, ctx)
    expect(mockedLogDevError).toHaveBeenCalled()
    expect(mockedNotyfError).toHaveBeenCalledWith('Something went wrong. Please try again.')
    expect(result).toEqual({})
  })

  it('should still dispatch loadingHide on failure', async () => {
    mockedFetchProductHtml.mockRejectedValueOnce(new Error('fail'))
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    await updateProducts({ page: 1 }, ctx)
    expect(mockedDispatchLoadingHide).toHaveBeenCalled()
  })

  it('should handle params without page (requestedPage undefined)', async () => {
    const ctx = makeProductListCtx({ productsWrapper: makeProductListWrapper() })
    const result = await updateProducts({}, ctx)
    expect(result).toMatchObject({ currentPage: expect.any(Number) as number, maxPages: expect.any(Number) as number })
    expect(mockedScrollToTop).not.toHaveBeenCalled()
  })
})
