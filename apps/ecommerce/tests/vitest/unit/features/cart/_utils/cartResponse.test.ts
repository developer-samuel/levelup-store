import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'

mockAxios()

import axios from 'axios'

import { isCartResponse, getCartErrorMessage } from '@/ts/features/cart/_utils/cartResponse'

const mockedIsAxiosError = vi.mocked(axios.isAxiosError)

describe('isCartResponse()', () => {
  it('should return true for a valid CartResponse object', () => {
    expect(isCartResponse({ success: true, message: 'ok' })).toBe(true)
  })

  it('should return true when only success key is present', () => {
    expect(isCartResponse({ success: false })).toBe(true)
  })

  it('should return false for null', () => {
    expect(isCartResponse(null)).toBe(false)
  })

  it('should return false for an array', () => {
    expect(isCartResponse([{ success: true }])).toBe(false)
  })

  it('should return false for a primitive', () => {
    expect(isCartResponse('string')).toBe(false)
  })

  it('should return false for an object without success key', () => {
    expect(isCartResponse({ message: 'no success key' })).toBe(false)
  })
})

describe('getCartErrorMessage()', () => {
  afterEach(() => {
    vi.clearAllMocks()
  })

  it('should return the message from axios error response when it is a CartResponse', () => {
    mockedIsAxiosError.mockReturnValue(true)
    const error = { response: { data: { success: false, message: 'Item not found.' } } }

    expect(getCartErrorMessage(error)).toBe('Item not found.')
  })

  it('should return fallback message when axios error response has no message', () => {
    mockedIsAxiosError.mockReturnValue(true)
    const error = { response: { data: { success: false } } }

    expect(getCartErrorMessage(error)).toBe('Something went wrong.')
  })

  it('should return fallback message when error is not an axios error', () => {
    mockedIsAxiosError.mockReturnValue(false)

    expect(getCartErrorMessage(new Error('network error'))).toBe('Something went wrong.')
  })

  it('should return fallback message for unknown error', () => {
    mockedIsAxiosError.mockReturnValue(false)

    expect(getCartErrorMessage('unexpected')).toBe('Something went wrong.')
  })
})
