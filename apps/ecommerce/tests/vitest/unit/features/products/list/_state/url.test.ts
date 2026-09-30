import { mockUtilsQuery } from '@/tests/_support/mocks/shared/utils.mocks'

mockUtilsQuery()

import { buildQueryString } from '@/ts/shared/utils/query'

import { updateUrlState } from '@/ts/features/products/list/_state/url'

const mockedBuildQueryString = vi.mocked(buildQueryString)

describe('updateUrlState()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
    vi.spyOn(history, 'pushState').mockImplementation(() => {})

    Object.defineProperty(window, 'location', {
      value: { pathname: '/products' },
      writable: true,
    })
  })

  it('should call buildQueryString with provided params', () => {
    mockedBuildQueryString.mockReturnValueOnce('brand=nike')
    const params = { brand: 'nike', page: 1 }

    updateUrlState(params)

    expect(mockedBuildQueryString).toHaveBeenCalledWith(params)
  })

  it('should call history.pushState with url containing query string', () => {
    mockedBuildQueryString.mockReturnValueOnce('brand=nike&page=1')

    updateUrlState({ brand: 'nike', page: 1 })

    expect(history.pushState).toHaveBeenCalledWith(null, '', '/products?brand=nike&page=1')
  })

  it('should call history.pushState without query string when buildQueryString returns empty', () => {
    mockedBuildQueryString.mockReturnValueOnce('')

    updateUrlState({})

    expect(history.pushState).toHaveBeenCalledWith(null, '', '/products')
  })

  it('should return the constructed url with query string', () => {
    mockedBuildQueryString.mockReturnValueOnce('sort=asc')

    const result = updateUrlState({ sort: 'asc' })

    expect(result).toBe('/products?sort=asc')
  })

  it('should return pathname only when query string is empty', () => {
    mockedBuildQueryString.mockReturnValueOnce('')

    const result = updateUrlState({})

    expect(result).toBe('/products')
  })
})
