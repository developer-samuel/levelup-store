import { mockHttpSubmitFormData } from '@/tests/_support/mocks/core/http.mocks'

mockHttpSubmitFormData()

import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import orderSubmit from '@/ts/features/orders/create/_services/orderService'

const mockedSubmitFormData = vi.mocked(submitFormData)

describe('orderSubmit()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should call submitFormData with correct URL', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)

    await orderSubmit(new FormData())

    expect(mockedSubmitFormData).toHaveBeenCalledWith(
      '/orders/store',
      expect.anything(),
      expect.anything(),
      expect.anything(),
    )
  })

  it('should pass formData to submitFormData', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)
    const formData = new FormData()
    formData.append('name', 'John')

    await orderSubmit(formData)

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), formData, expect.anything(), expect.anything())
  })

  it('should call submitFormData with withCredentials=true', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)

    await orderSubmit(new FormData())

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), expect.anything(), true, expect.anything())
  })

  it('should call submitFormData with checkSubmitting=true', async () => {
    mockedSubmitFormData.mockResolvedValueOnce(undefined)

    await orderSubmit(new FormData())

    expect(mockedSubmitFormData).toHaveBeenCalledWith(expect.anything(), expect.anything(), expect.anything(), true)
  })

  it('should return the result from submitFormData', async () => {
    const expected = { message: 'Order created.' }
    mockedSubmitFormData.mockResolvedValueOnce(expected)

    const result = await orderSubmit(new FormData())

    expect(result).toBe(expected)
  })
})
