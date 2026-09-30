import { mockHttpSubmitFormData } from '@/tests/_support/mocks/core/http.mocks'

mockHttpSubmitFormData()

import { destroyData } from '@/ts/core/http/_services/destroyData'
import { submitFormData } from '@/ts/core/http/_services/submitFormData'

const mockedSubmitFormData = vi.mocked(submitFormData)

describe('destroyData()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call submitFormData with the correct url', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await destroyData('/admin/products', 5)

    expect(mockedSubmitFormData).toHaveBeenCalledWith('/admin/products', expect.any(FormData))
  })

  it('should append id as string to FormData', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await destroyData('/admin/products', 42)

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('id')).toBe('42')
  })

  it('should accept string id', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await destroyData('/admin/products', 'abc-123')

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('id')).toBe('abc-123')
  })

  it('should return the result from submitFormData', async () => {
    const expected = { success: true }
    mockedSubmitFormData.mockResolvedValueOnce(expected)

    const result = await destroyData('/admin/products', 1)

    expect(result).toBe(expected)
  })

  it('should return null when submitFormData returns null', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(null)

    const result = await destroyData('/admin/products', 1)

    expect(result).toBeNull()
  })
})
