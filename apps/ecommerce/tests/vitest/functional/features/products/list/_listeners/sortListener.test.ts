vi.mock('@/ts/features/products/list/_handlers/sortHandler', () => ({
  handleSort: vi.fn(),
}))

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { handleSort } from '@/ts/features/products/list/_handlers/sortHandler'
import { attachSortListener } from '@/ts/features/products/list/_listeners/sortListener'

const mockedHandleSort = vi.mocked(handleSort)

const ctx = {} as ProductListInstance

describe('attachSortListener()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedHandleSort.mockResolvedValue(undefined)
  })

  it('should do nothing when sortSelect is null', () => {
    expect(() => attachSortListener(ctx, null)).not.toThrow()
    expect(mockedHandleSort).not.toHaveBeenCalled()
  })

  it('should call handleSort when change event fires', async () => {
    const select = document.createElement('select')

    attachSortListener(ctx, select)
    select.dispatchEvent(new Event('change'))

    await vi.waitFor(() => expect(mockedHandleSort).toHaveBeenCalledTimes(1))
  })

  it('should pass event and ctx to handleSort', async () => {
    const select = document.createElement('select')

    attachSortListener(ctx, select)
    select.dispatchEvent(new Event('change'))

    await vi.waitFor(() => expect(mockedHandleSort).toHaveBeenCalledWith(expect.any(Event), ctx))
  })
})
