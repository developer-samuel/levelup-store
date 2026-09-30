import { mockUtilsQuery } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockProductsListFilter } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListFilter()
mockUtilsQuery()

import type { StringRecord } from '@/ts/shared/types'
import { parseQueryParams } from '@/ts/shared/utils/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { setupSubtypeFilter } from '@/ts/features/products/list/_filters/subtypeFilter'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

const mockedBindFilter = vi.mocked(bindFilter)
const mockedParseQueryParams = vi.mocked(parseQueryParams)

const ctx = {} as ProductListInstance

function getValueCallback(): (item: HTMLElement) => StringRecord {
  return mockedBindFilter.mock.calls[0]?.[2] as (item: HTMLElement) => StringRecord
}

function makeItem(subtype: string): HTMLElement {
  const el = document.createElement('div')
  el.dataset.subtype = subtype
  return el
}

describe('setupSubtypeFilter()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call bindFilter with correct selector and eventType click', () => {
    setupSubtypeFilter(ctx)
    expect(mockedBindFilter).toHaveBeenCalledWith(ctx, '[data-subtype]', expect.any(Function), { eventType: 'click' })
  })

  it('should add subtype to result when not in current params', () => {
    mockedParseQueryParams.mockReturnValueOnce({})
    setupSubtypeFilter(ctx)

    const result = getValueCallback()(makeItem('sneakers'))

    expect(result).toEqual({ subtype: 'sneakers' })
  })

  it('should add active class to item when subtype is added', () => {
    mockedParseQueryParams.mockReturnValueOnce({})
    setupSubtypeFilter(ctx)

    const item = makeItem('sneakers')
    getValueCallback()(item)

    expect(item.classList.contains('products__filter-list-item--active')).toBe(true)
  })

  it('should remove subtype from result when already in current params', () => {
    mockedParseQueryParams.mockReturnValueOnce({ subtype: 'sneakers,boots' })
    setupSubtypeFilter(ctx)

    const result = getValueCallback()(makeItem('sneakers'))

    expect(result).toEqual({ subtype: 'boots' })
  })

  it('should remove active class from item when subtype is removed', () => {
    mockedParseQueryParams.mockReturnValueOnce({ subtype: 'sneakers' })
    setupSubtypeFilter(ctx)

    const item = makeItem('sneakers')
    item.classList.add('products__filter-list-item--active')
    getValueCallback()(item)

    expect(item.classList.contains('products__filter-list-item--active')).toBe(false)
  })

  it('should normalize subtype to lowercase and replace spaces with dashes', () => {
    mockedParseQueryParams.mockReturnValueOnce({})
    setupSubtypeFilter(ctx)

    const item = makeItem('Running Shoes')
    const result = getValueCallback()(item)

    expect(result).toEqual({ subtype: 'running-shoes' })
  })

  it('should return empty subtype string when last subtype is removed', () => {
    mockedParseQueryParams.mockReturnValueOnce({ subtype: 'sneakers' })
    setupSubtypeFilter(ctx)

    const result = getValueCallback()(makeItem('sneakers'))

    expect(result).toEqual({ subtype: '' })
  })

  it('should append new subtype to existing ones', () => {
    mockedParseQueryParams.mockReturnValueOnce({ subtype: 'boots' })
    setupSubtypeFilter(ctx)

    const result = getValueCallback()(makeItem('sneakers'))

    expect(result).toEqual({ subtype: 'boots,sneakers' })
  })

  it('should use empty string when dataset.subtype is undefined', () => {
    mockedParseQueryParams.mockReturnValueOnce({})
    setupSubtypeFilter(ctx)

    const item = document.createElement('div')
    const result = getValueCallback()(item)

    expect(result).toEqual({ subtype: '' })
  })
})
