import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'

mockAxios()

import axios from 'axios'

import { fetchProductHtml } from '@/ts/features/products/list/_services/productsService'

const mockedGet = vi.mocked(axios.get)

describe('fetchProductHtml()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return response data on success', async () => {
    mockedGet.mockResolvedValueOnce({ data: '<div>Products</div>' })

    const result = await fetchProductHtml('/products?page=1')

    expect(result).toBe('<div>Products</div>')
  })

  it('should call axios.get with the correct URL', async () => {
    mockedGet.mockResolvedValueOnce({ data: '' })

    await fetchProductHtml('/products?brand=nike')

    expect(mockedGet).toHaveBeenCalledWith('/products?brand=nike', expect.anything())
  })

  it('should send X-Requested-With: XMLHttpRequest header', async () => {
    mockedGet.mockResolvedValueOnce({ data: '' })

    await fetchProductHtml('/products')

    expect(mockedGet).toHaveBeenCalledWith(
      expect.anything(),
      expect.objectContaining({
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
      }),
    )
  })

  it('should propagate error when axios.get throws', async () => {
    mockedGet.mockRejectedValueOnce(new Error('Network error'))

    await expect(fetchProductHtml('/products')).rejects.toThrow('Network error')
  })
})
