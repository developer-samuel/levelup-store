import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'
import { mockUtilsLogger } from '@/tests/_support/mocks/shared/utils.mocks'

mockAxios()
mockUtilsLogger()

import type { AxiosInstance, AxiosResponse } from 'axios'
import axios from 'axios'

import { applyErrorInterceptor } from '@/ts/core/http/_interceptors/error.interceptor'

import { logDevError } from '@/ts/shared/utils/logger'

const mockedIsCancel = vi.mocked(axios.isCancel)
const mockedLogDevError = vi.mocked(logDevError)

function buildApi(): AxiosInstance {
  const handlers: { onFulfilled: (r: AxiosResponse) => AxiosResponse; onRejected: (e: unknown) => unknown }[] = []

  const api = {
    interceptors: {
      response: {
        use: vi.fn((onFulfilled: (r: AxiosResponse) => AxiosResponse, onRejected: (e: unknown) => unknown) => {
          handlers.push({ onFulfilled, onRejected })
        }),
      },
    },
    triggerError: (error: unknown): Promise<unknown> => {
      const handler = handlers[0]
      return (handler?.onRejected(error) as Promise<unknown>) ?? Promise.reject(error)
    },
    triggerSuccess: (response: AxiosResponse): Promise<AxiosResponse> => {
      const handler = handlers[0]
      return Promise.resolve(handler?.onFulfilled(response) as AxiosResponse)
    },
  }

  return api as unknown as AxiosInstance
}

describe('applyErrorInterceptor()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should log error when it is not a cancelled request', async () => {
    mockedIsCancel.mockReturnValue(false)
    const api = buildApi()
    applyErrorInterceptor(api)

    const error = new Error('Network error')

    expect(api.interceptors.response.use).toHaveBeenCalled()
    await expect(
      (api as unknown as { triggerError: (e: unknown) => Promise<unknown> }).triggerError(error),
    ).rejects.toThrow()

    expect(mockedLogDevError).toHaveBeenCalledWith('[API]', error)
  })

  it('should not log error when it is a cancelled request', async () => {
    mockedIsCancel.mockReturnValue(true)
    const api = buildApi()
    applyErrorInterceptor(api)

    const error = new Error('Cancelled')

    await expect(
      (api as unknown as { triggerError: (e: unknown) => Promise<unknown> }).triggerError(error),
    ).rejects.toThrow()

    expect(mockedLogDevError).not.toHaveBeenCalled()
  })

  it('should pass through successful responses unchanged', async () => {
    const api = buildApi()
    applyErrorInterceptor(api)

    const response = { data: { ok: true }, status: 200 } as AxiosResponse

    const result = await (
      api as unknown as { triggerSuccess: (r: AxiosResponse) => Promise<AxiosResponse> }
    ).triggerSuccess(response)

    expect(result).toBe(response)
  })

  it('should always reject the error', async () => {
    mockedIsCancel.mockReturnValue(false)
    const api = buildApi()
    applyErrorInterceptor(api)

    const error = new Error('Some error')

    await expect(
      (api as unknown as { triggerError: (e: unknown) => Promise<unknown> }).triggerError(error),
    ).rejects.toBe(error)
  })
})
