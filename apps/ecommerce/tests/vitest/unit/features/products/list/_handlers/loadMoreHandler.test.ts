vi.mock('@/ts/features/products/list/_interactions/loadMore', () => ({
  loadMoreProducts: vi.fn(),
}))

import { makeMouseEvent } from '@/tests/_support/fakers/events.fakers'
import { makeProductListCtx } from '@/tests/_support/fakers/features/products/list.fakers'

import { handleLoadMoreClick } from '@/ts/features/products/list/_handlers/loadMoreHandler'
import { loadMoreProducts } from '@/ts/features/products/list/_interactions/loadMore'

const mockedLoadMoreProducts = vi.mocked(loadMoreProducts)

function makeLoadMoreBtn(): HTMLButtonElement {
  const btn = document.createElement('button')
  btn.id = 'load-more'
  return btn
}

describe('handleLoadMoreClick()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedLoadMoreProducts.mockResolvedValue(undefined)
  })

  it('should do nothing when target has no #load-more ancestor', () => {
    const div = document.createElement('div')
    handleLoadMoreClick(makeMouseEvent(div), makeProductListCtx())
    expect(mockedLoadMoreProducts).not.toHaveBeenCalled()
  })

  it('should do nothing when target is null', () => {
    handleLoadMoreClick(makeMouseEvent(null), makeProductListCtx())
    expect(mockedLoadMoreProducts).not.toHaveBeenCalled()
  })

  it('should do nothing when ctx.isLoading is true', () => {
    handleLoadMoreClick(makeMouseEvent(makeLoadMoreBtn()), makeProductListCtx({ isLoading: true }))
    expect(mockedLoadMoreProducts).not.toHaveBeenCalled()
  })

  it('should call loadMoreProducts when #load-more is clicked and not loading', async () => {
    const ctx = makeProductListCtx()
    handleLoadMoreClick(makeMouseEvent(makeLoadMoreBtn()), ctx)

    await vi.waitFor(() => expect(mockedLoadMoreProducts).toHaveBeenCalledWith(ctx))
  })

  it('should prevent default when #load-more is clicked', () => {
    const event = makeMouseEvent(makeLoadMoreBtn())
    const preventDefaultSpy = vi.spyOn(event, 'preventDefault')

    handleLoadMoreClick(event, makeProductListCtx())

    expect(preventDefaultSpy).toHaveBeenCalledTimes(1)
  })

  it('should find #load-more via closest when clicking inner element', async () => {
    const btn = makeLoadMoreBtn()
    const inner = document.createElement('span')
    btn.appendChild(inner)
    document.body.appendChild(btn)

    const ctx = makeProductListCtx()
    handleLoadMoreClick(makeMouseEvent(inner), ctx)

    await vi.waitFor(() => expect(mockedLoadMoreProducts).toHaveBeenCalledWith(ctx))

    document.body.innerHTML = ''
  })
})
