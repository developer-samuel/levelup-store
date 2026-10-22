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

  it.each([
    [[], ''],
    [[makeCheckbox('Nike'), makeCheckbox('Adidas')], 'nike,adidas'],
    [[makeCheckbox('PUMA')], 'puma'],
    [[makeCheckbox('New Balance')], 'new-balance'],
    [[makeCheckbox('  Nike  ')], 'nike'],
  ] as [HTMLInputElement[], string][])('should return brand=%s from checkboxes %s', (checkboxes, expected) => {
    mockedQueryAll.mockReturnValueOnce(checkboxes as unknown as HtmlElList)
    setupBrandFilter(ctx)

    const result = getValueCallback()()

    expect(result).toEqual({ brand: expected })
  })
})
