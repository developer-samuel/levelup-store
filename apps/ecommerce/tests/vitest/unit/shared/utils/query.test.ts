import { getQueryParams, buildQueryString, parseQueryParams } from '@/ts/shared/utils/query'

describe('getQueryParams()', () => {
  it('should return value of existing param', () => {
    Object.defineProperty(window, 'location', { value: { search: '?page=2&sort=asc' }, writable: true })
    expect(getQueryParams('page')).toBe('2')
  })

  it('should return null for missing param', () => {
    Object.defineProperty(window, 'location', { value: { search: '?page=2' }, writable: true })
    expect(getQueryParams('sort')).toBeNull()
  })

  it('should return null when search is empty', () => {
    Object.defineProperty(window, 'location', { value: { search: '' }, writable: true })
    expect(getQueryParams('page')).toBeNull()
  })
})

describe('buildQueryString()', () => {
  it('should build query string from params', () => {
    expect(buildQueryString({ page: 1, sort: 'asc' })).toBe('page=1&sort=asc')
  })

  it('should omit null values', () => {
    expect(buildQueryString({ page: 1, sort: null })).toBe('page=1')
  })

  it('should omit undefined values', () => {
    expect(buildQueryString({ page: 1, sort: undefined })).toBe('page=1')
  })

  it('should omit empty string values', () => {
    expect(buildQueryString({ page: 1, sort: '' })).toBe('page=1')
  })

  it('should trim, lowercase and replace spaces with dashes', () => {
    expect(buildQueryString({ brand: '  Hello World  ' })).toBe('brand=hello-world')
  })

  it('should replace plus signs with dashes', () => {
    expect(buildQueryString({ brand: 'foo+bar' })).toBe('brand=foo-bar')
  })

  it('should handle numeric values', () => {
    expect(buildQueryString({ page: 3 })).toBe('page=3')
  })

  it('should return empty string when all values are omitted', () => {
    expect(buildQueryString({ a: null, b: undefined, c: '' })).toBe('')
  })
})

describe('parseQueryParams()', () => {
  it('should return empty object when search is empty', () => {
    Object.defineProperty(window, 'location', { value: { search: '' }, writable: true })
    expect(parseQueryParams()).toEqual({})
  })

  it('should parse key=value pairs', () => {
    Object.defineProperty(window, 'location', { value: { search: '?page=2&sort=asc' }, writable: true })
    expect(parseQueryParams()).toEqual({ page: '2', sort: 'asc' })
  })

  it('should decode URI-encoded values', () => {
    Object.defineProperty(window, 'location', { value: { search: '?brand=hello%20world' }, writable: true })
    expect(parseQueryParams()).toEqual({ brand: 'hello-world' })
  })

  it('should replace spaces with dashes', () => {
    Object.defineProperty(window, 'location', { value: { search: '?brand=foo bar' }, writable: true })
    expect(parseQueryParams()).toEqual({ brand: 'foo-bar' })
  })

  it('should store undefined for params without values', () => {
    Object.defineProperty(window, 'location', { value: { search: '?novalue' }, writable: true })
    expect(parseQueryParams()).toEqual({ novalue: undefined })
  })
})
