import { mockUtilsQuery } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockNotyfAlert } from '@/tests/_support/mocks/plugins/notyf.mocks'
import { mockProductsListUpdateProducts } from '@/tests/_support/mocks/features/products/list.mocks'

mockNotyfAlert()
mockUtilsQuery()
mockProductsListUpdateProducts()

import { makeProductListCtx } from '@/tests/_support/fakers/features/products/list.fakers'

import type { StringRecord } from '@/ts/shared/types'
import { parseQueryParams } from '@/ts/shared/utils/query'

import { handleFilter } from '@/ts/features/products/list/_handlers/filterHandler'
import { updateProducts } from '@/ts/features/products/list/_interactions/updateProducts'
import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

const mockedParseQueryParams = vi.mocked(parseQueryParams)
const mockedUpdateProducts = vi.mocked(updateProducts)
const mockedNotyfError = vi.mocked(NotyfAlert.error)

describe('handleFilter()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    mockedParseQueryParams.mockReturnValue({})
    mockedUpdateProducts.mockResolvedValue({})
  })

  it('should reset page to 1', async () => {
    const ctx = makeProductListCtx({ page: 3 })
    await handleFilter(document.createElement('div'), ctx, () => ({}))
    expect(ctx.page).toBe(1)
  })

  it('should call updateProducts with merged params and page 1', async () => {
    mockedParseQueryParams.mockReturnValueOnce({ sort: 'asc' })
    const ctx = makeProductListCtx({ page: 2 })
    const el = document.createElement('div')

    await handleFilter(el, ctx, () => ({ brand: 'nike' }))

    expect(mockedUpdateProducts).toHaveBeenCalledWith({ sort: 'asc', brand: 'nike', page: 1 }, ctx)
  })

  it('should pass element to valueCallback', async () => {
    const ctx = makeProductListCtx({ page: 2 })
    const el = document.createElement('div')
    const valueCallback = vi.fn().mockReturnValue({})

    await handleFilter(el, ctx, valueCallback)

    expect(valueCallback).toHaveBeenCalledWith(el)
  })

  it('should use empty object when valueCallback returns null', async () => {
    mockedParseQueryParams.mockReturnValueOnce({})
    const ctx = makeProductListCtx({ page: 2 })

    await handleFilter(document.createElement('div'), ctx, () => null as unknown as StringRecord)

    expect(mockedUpdateProducts).toHaveBeenCalledWith({ page: 1 }, ctx)
  })

  it('should show error alert when updateProducts throws', async () => {
    mockedUpdateProducts.mockRejectedValueOnce(new Error('Network error'))
    const ctx = makeProductListCtx({ page: 2 })

    await handleFilter(document.createElement('div'), ctx, () => ({}))

    expect(mockedNotyfError).toHaveBeenCalledWith('Something went wrong. Please try again.')
  })

  it('should not throw when updateProducts rejects', async () => {
    mockedUpdateProducts.mockRejectedValueOnce(new Error('fail'))
    const ctx = makeProductListCtx({ page: 2 })

    await expect(handleFilter(document.createElement('div'), ctx, () => ({}))).resolves.toBeUndefined()
  })
})
