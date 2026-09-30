vi.mock('@/ts/core/http/_services/getApiData', () => ({
  getApiData: vi.fn(),
}))

import { getApiData } from '@/ts/core/http/_services/getApiData'

import { fetchDatatableData } from '@/ts/plugins/datatables/_services/datatablesService'

const mockedGetApiData = vi.mocked(getApiData)

describe('fetchDatatableData()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call getApiData with url and safe=true', async () => {
    mockedGetApiData.mockResolvedValueOnce([])

    await fetchDatatableData('/api/products')

    expect(mockedGetApiData).toHaveBeenCalledWith('/api/products', true)
  })

  it('should return empty array when getApiData returns null', async () => {
    mockedGetApiData.mockResolvedValueOnce(null)

    const result = await fetchDatatableData('/api/products')

    expect(result).toEqual([])
  })

  it('should return data directly when it is an array', async () => {
    const items = [{ id: 1 }, { id: 2 }]
    mockedGetApiData.mockResolvedValueOnce(items)

    const result = await fetchDatatableData('/api/products')

    expect(result).toBe(items)
  })

  it('should return empty array when data is not an array and no dataKey', async () => {
    mockedGetApiData.mockResolvedValueOnce({ id: 1, name: 'test' })

    const result = await fetchDatatableData('/api/products')

    expect(result).toEqual([])
  })

  it('should return nested array when dataKey matches', async () => {
    const items = [{ id: 1 }, { id: 2 }]
    mockedGetApiData.mockResolvedValueOnce({ products: items })

    const result = await fetchDatatableData('/api/products', { dataKey: 'products' })

    expect(result).toBe(items)
  })

  it('should return empty array when dataKey does not match', async () => {
    mockedGetApiData.mockResolvedValueOnce({ products: [{ id: 1 }] })

    const result = await fetchDatatableData('/api/products', { dataKey: 'items' })

    expect(result).toEqual([])
  })

  it('should return empty array when dataKey value is not an array', async () => {
    mockedGetApiData.mockResolvedValueOnce({ products: 'not-an-array' })

    const result = await fetchDatatableData('/api/products', { dataKey: 'products' })

    expect(result).toEqual([])
  })

  it('should return empty array when data is null with dataKey', async () => {
    mockedGetApiData.mockResolvedValueOnce(null)

    const result = await fetchDatatableData('/api/products', { dataKey: 'items' })

    expect(result).toEqual([])
  })

  it('should return empty array when data is a primitive with dataKey', async () => {
    mockedGetApiData.mockResolvedValueOnce(42)

    const result = await fetchDatatableData('/api/products', { dataKey: 'items' })

    expect(result).toEqual([])
  })
})
