import { mockProductsListFilter } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListFilter()

import type { StringRecord } from '@/ts/shared/types'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { setupPriceFilter } from '@/ts/features/products/list/_filters/priceFilter'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

const mockedBindFilter = vi.mocked(bindFilter)

const ctx = {} as ProductListInstance

function getValueCallback(): () => StringRecord {
  return mockedBindFilter.mock.calls[0]?.[2] as () => StringRecord
}

function setupPriceInputs(minValue = '', maxValue = ''): void {
  const min = document.createElement('input')
  min.id = 'minPrice'
  min.value = minValue

  const max = document.createElement('input')
  max.id = 'maxPrice'
  max.value = maxValue

  document.body.append(min, max)
}

describe('setupPriceFilter()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    document.body.innerHTML = ''
  })

  it('should call bindFilter with correct selector', () => {
    setupPriceFilter(ctx)
    expect(mockedBindFilter).toHaveBeenCalledWith(ctx, '#minPrice, #maxPrice', expect.any(Function), {
      eventType: 'change',
    })
  })

  it('should return minPrice and maxPrice from DOM inputs', () => {
    setupPriceInputs('10', '200')
    setupPriceFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ minPrice: '10', maxPrice: '200' })
  })

  it('should return empty strings when inputs do not exist', () => {
    setupPriceFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ minPrice: '', maxPrice: '' })
  })

  it('should return empty string for minPrice when only maxPrice exists', () => {
    const max = document.createElement('input')
    max.id = 'maxPrice'
    max.value = '150'
    document.body.appendChild(max)

    setupPriceFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ minPrice: '', maxPrice: '150' })
  })
})
