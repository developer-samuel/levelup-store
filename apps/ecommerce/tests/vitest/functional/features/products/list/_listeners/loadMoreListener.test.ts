vi.mock('@/ts/features/products/list/_handlers/loadMoreHandler', () => ({
  handleLoadMoreClick: vi.fn(),
}))

import { makeProductListCtx } from '@/tests/_support/fakers/features/products/list.fakers'

import { handleLoadMoreClick } from '@/ts/features/products/list/_handlers/loadMoreHandler'
import { attachLoadMoreListener } from '@/ts/features/products/list/_listeners/loadMoreListener'

const mockedHandleLoadMoreClick = vi.mocked(handleLoadMoreClick)

describe('attachLoadMoreListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should do nothing when productsWrapper is null', () => {
    expect(() =>
      attachLoadMoreListener(makeProductListCtx({ productsWrapper: null as unknown as HTMLElement })),
    ).not.toThrow()
    expect(mockedHandleLoadMoreClick).not.toHaveBeenCalled()
  })

  it('should call handleLoadMoreClick when productsWrapper is clicked', () => {
    const wrapper = document.createElement('div')
    const ctx = makeProductListCtx({ productsWrapper: wrapper })

    attachLoadMoreListener(ctx)
    wrapper.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(mockedHandleLoadMoreClick).toHaveBeenCalledTimes(1)
  })

  it('should pass the event and ctx to handleLoadMoreClick', () => {
    const wrapper = document.createElement('div')
    const ctx = makeProductListCtx({ productsWrapper: wrapper })

    attachLoadMoreListener(ctx)
    wrapper.dispatchEvent(new MouseEvent('click', { bubbles: true }))

    expect(mockedHandleLoadMoreClick).toHaveBeenCalledWith(expect.any(MouseEvent), ctx)
  })
})
