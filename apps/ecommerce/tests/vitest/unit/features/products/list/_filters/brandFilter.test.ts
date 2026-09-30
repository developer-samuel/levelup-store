import { mockUtilsDomQuery } from '@/tests/_support/mocks/shared/utils.mocks'
import { mockProductsListFilter } from '@/tests/_support/mocks/features/products/list.mocks'

mockProductsListFilter()
mockUtilsDomQuery()

import { makeCheckbox } from '@/tests/_support/fakers/dom.fakers'

import type { HtmlElList, StringRecord } from '@/ts/shared/types'
import { queryAll } from '@/ts/shared/utils/dom/query'

import type { ProductListInstance } from '@/ts/features/products/list/types'
import { setupBrandFilter } from '@/ts/features/products/list/_filters/brandFilter'
import { bindFilter } from '@/ts/features/products/list/_interactions/filter'

const mockedBindFilter = vi.mocked(bindFilter)
const mockedQueryAll = vi.mocked(queryAll)

const ctx = {} as ProductListInstance

function getValueCallback(): () => StringRecord {
  return mockedBindFilter.mock.calls[0]?.[2] as () => StringRecord
}

describe('setupBrandFilter()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call bindFilter with correct selector', () => {
    setupBrandFilter(ctx)
    expect(mockedBindFilter).toHaveBeenCalledWith(ctx, 'input[name="brand[]"]', expect.any(Function))
  })

  it('should return empty brand string when no checkboxes are checked', () => {
    mockedQueryAll.mockReturnValueOnce([] as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: '' })
  })

  it('should return joined brand values from checked checkboxes', () => {
    mockedQueryAll.mockReturnValueOnce([makeCheckbox('Nike'), makeCheckbox('Adidas')] as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: 'nike,adidas' })
  })

  it('should normalize brand values to lowercase', () => {
    mockedQueryAll.mockReturnValueOnce([makeCheckbox('PUMA')] as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: 'puma' })
  })

  it('should replace spaces with dashes in brand values', () => {
    mockedQueryAll.mockReturnValueOnce([makeCheckbox('New Balance')] as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: 'new-balance' })
  })

  it('should trim whitespace from brand values', () => {
    mockedQueryAll.mockReturnValueOnce([makeCheckbox('  Nike  ')] as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: 'nike' })
  })
})
