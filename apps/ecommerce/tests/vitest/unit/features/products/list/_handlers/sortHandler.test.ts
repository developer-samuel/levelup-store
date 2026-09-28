import { mockUtilsQuery, mockUtilsScroll } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockSharedEventsLoading } from '@/tests/_support/mocks/shared/events.mocks'
import { mockProductsListUpdateProducts } from '@/tests/_support/mocks/features/products/list.mocks'

mockUtilsQuery()
mockUtilsScroll()
mockSharedEventsLoading()
mockProductsListUpdateProducts()

import { makeProductListCtx } from '@/tests/_support/fakers/features/products/list.fakers'

import { parseQueryParams } from '@/ts/shared/utils/query'
import { scrollToTop } from '@/ts/shared/utils/scroll'
import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

import { handleSort } from '@/ts/features/products/list/_handlers/sortHandler'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'

const mockedParseQueryParams = vi.mocked(parseQueryParams)
const mockedScrollToTop = vi.mocked(scrollToTop)
const mockedLoadingShow = vi.mocked(dispatchLoadingShow)
const mockedLoadingHide = vi.mocked(dispatchLoadingHide)
const mockedUpdateProducts = vi.mocked(updateProducts)

function makeSelectEvent(value: string): Event {
  const select = document.createElement('select')
  const option = document.createElement('option')
  option.value = value
  select.appendChild(option)
  select.value = value
  const event = new Event('change')
  Object.defineProperty(event, 'target', { value: select, writable: false })
  return event
}

function makeNonSelectEvent(): Event {
  const event = new Event('change')
  Object.defineProperty(event, 'target', { value: document.createElement('div'), writable: false })
  return event
}

describe('handleSort()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedParseQueryParams.mockReturnValue({})
    mockedUpdateProducts.mockResolvedValue({})
  })

  it('should reset page to 1', async () => {
    const ctx = makeProductListCtx({ page: 2 })
    await handleSort(makeSelectEvent('asc'), ctx)
    expect(ctx.page).toBe(1)
  })

  it('should call scrollToTop', async () => {
    await handleSort(makeSelectEvent('asc'), makeProductListCtx({ page: 2 }))
    expect(mockedScrollToTop).toHaveBeenCalledTimes(1)
  })

  it('should call dispatchLoadingShow', async () => {
    await handleSort(makeSelectEvent('asc'), makeProductListCtx({ page: 2 }))
    expect(mockedLoadingShow).toHaveBeenCalledTimes(1)
  })

  it('should call dispatchLoadingHide after updateProducts resolves', async () => {
    await handleSort(makeSelectEvent('asc'), makeProductListCtx({ page: 2 }))
    expect(mockedLoadingHide).toHaveBeenCalledTimes(1)
  })

  it('should call dispatchLoadingHide even when updateProducts throws', async () => {
    mockedUpdateProducts.mockRejectedValueOnce(new Error('fail'))
    await expect(handleSort(makeSelectEvent('asc'), makeProductListCtx({ page: 2 }))).rejects.toThrow('fail')
    expect(mockedLoadingHide).toHaveBeenCalledTimes(1)
  })

  it('should call updateProducts with merged params, sort value and page 1', async () => {
    mockedParseQueryParams.mockReturnValueOnce({ brand: 'nike' })
    const ctx = makeProductListCtx({ page: 2 })

    await handleSort(makeSelectEvent('price-asc'), ctx)

    expect(mockedUpdateProducts).toHaveBeenCalledWith({ brand: 'nike', sort: 'price-asc', page: 1 }, ctx)
  })

  it('should use empty string for sort when target is not a select element', async () => {
    const ctx = makeProductListCtx({ page: 2 })

    await handleSort(makeNonSelectEvent(), ctx)

    expect(mockedUpdateProducts).toHaveBeenCalledWith(expect.objectContaining({ sort: '' }), ctx)
  })
})
