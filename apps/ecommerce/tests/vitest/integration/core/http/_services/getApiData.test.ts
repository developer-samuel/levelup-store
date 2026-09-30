import { mockHttpApi } from '@/tests/_support/mocks/core/http.mocks'

mockHttpApi()

import api from '@/ts/core/http/api'
import { getApiData } from '@/ts/core/http/_services/getApiData'

const mockedGet = vi.mocked(api.get)

describe('getApiData()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should return response data on success', async () => {
    mockedGet.mockResolvedValueOnce({ data: { id: 1, name: 'test' } })

    const result = await getApiData('/test')

    expect(result).toEqual({ id: 1, name: 'test' })
  })

  it('should return null when response data is null', async () => {
    mockedGet.mockResolvedValueOnce({ data: null })

    const result = await getApiData('/test')

    expect(result).toBeNull()
  })

  it('should return null on error when safe is true', async () => {
    mockedGet.mockRejectedValueOnce(new Error('Network error'))

    const result = await getApiData('/test', true)

    expect(result).toBeNull()
  })

  it('should throw on error when safe is false', async () => {
    mockedGet.mockRejectedValueOnce(new Error('Network error'))

    await expect(getApiData('/test', false)).rejects.toThrow('Network error')
  })

  it('should pass AbortSignal to api.get when provided', async () => {
    mockedGet.mockResolvedValueOnce({ data: {} })
    const signal = new AbortController().signal

    await getApiData('/test', true, false, signal)

    expect(mockedGet).toHaveBeenCalledWith('/test', expect.objectContaining({ signal }))
  })

  it('should not include signal in request when not provided', async () => {
    mockedGet.mockResolvedValueOnce({ data: {} })

    await getApiData('/test')

    const callArgs = mockedGet.mock.calls[0]?.[1]
    expect(callArgs).not.toHaveProperty('signal')
  })

  it('should pass withLoading flag to api.get', async () => {
    mockedGet.mockResolvedValueOnce({ data: {} })

    await getApiData('/test', true, true)

    expect(mockedGet).toHaveBeenCalledWith('/test', expect.objectContaining({ withLoading: true }))
  })
})
