import { mockHttpSubmitFormData } from '@/tests/_support/mocks/core/http.mocks'

mockHttpSubmitFormData()

import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import { cartAdd, cartRemove } from '@/ts/features/cart/_services/cartService'

const mockedSubmitFormData = vi.mocked(submitFormData)

function setupCsrfTokens(storeToken = 'store-token', destroyToken = 'destroy-token'): void {
  document.body.innerHTML = `
    <input id="csrf-cart-store" value="${storeToken}" />
    <input id="csrf-cart-destroy" value="${destroyToken}" />
  `
}

afterEach(() => {
  document.body.innerHTML = ''
  vi.clearAllMocks()
})

describe('cartAdd()', () => {
  it('should POST to /cart/store', async () => {
    setupCsrfTokens()
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartAdd(3)

    expect(mockedSubmitFormData).toHaveBeenCalledWith('/cart/store', expect.any(FormData))
  })

  it('should append variant_id to FormData', async () => {
    setupCsrfTokens()
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartAdd(42)

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('variant_id')).toBe('42')
  })

  it('should append csrf token from #csrf-cart-store to FormData', async () => {
    setupCsrfTokens('my-store-csrf')
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartAdd(1)

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('_csrf_token')).toBe('my-store-csrf')
  })

  it('should throw when #csrf-cart-store input is missing', async () => {
    document.body.innerHTML = ''

    await expect(cartAdd(1)).rejects.toThrow('CSRF token not found')
  })

  it('should return the result from submitFormData', async () => {
    setupCsrfTokens()
    const response = { success: true, totalItems: 2 }
    mockedSubmitFormData.mockResolvedValueOnce(response)

    const result = await cartAdd(1)

    expect(result).toBe(response)
  })
})

describe('cartRemove()', () => {
  it('should POST to /cart/destroy', async () => {
    setupCsrfTokens()
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartRemove(7)

    expect(mockedSubmitFormData).toHaveBeenCalledWith('/cart/destroy', expect.any(FormData))
  })

  it('should append item_id to FormData', async () => {
    setupCsrfTokens()
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartRemove(99)

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('item_id')).toBe('99')
  })

  it('should append csrf token from #csrf-cart-destroy to FormData', async () => {
    setupCsrfTokens('ignored', 'my-destroy-csrf')
    mockedSubmitFormData.mockResolvedValueOnce(null)

    await cartRemove(1)

    const formData = mockedSubmitFormData.mock.calls[0]?.[1] as FormData
    expect(formData.get('_csrf_token')).toBe('my-destroy-csrf')
  })

  it('should throw when #csrf-cart-destroy input is missing', async () => {
    document.body.innerHTML = ''

    await expect(cartRemove(1)).rejects.toThrow('CSRF token not found')
  })

  it('should return the result from submitFormData', async () => {
    setupCsrfTokens()
    const response = { success: true, totalItems: 1 }
    mockedSubmitFormData.mockResolvedValueOnce(response)

    const result = await cartRemove(1)

    expect(result).toBe(response)
  })
})
