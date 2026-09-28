import { mockAxios } from '@/tests/_support/mocks/_external/axios.mocks'

mockAxios()

import type { AxiosInstance, AxiosResponse, InternalAxiosRequestConfig } from 'axios'
import axios from 'axios'

import { applyLoadingInterceptor } from '@/ts/core/http/_interceptors/loading.interceptor'

import { LOADING_SHOW, LOADING_HIDE } from '@/ts/shared/events/loading'

const mockedIsAxiosError = vi.mocked(axios.isAxiosError)

type InterceptorHandler = {
  onFulfilled: (r: unknown) => unknown
  onRejected: (e: unknown) => unknown
}

function buildApi(): AxiosInstance & {
  triggerRequest: (config: Partial<InternalAxiosRequestConfig>) => Promise<unknown>
  triggerRequestError: (error: unknown) => Promise<unknown>
  triggerResponse: (response: Partial<AxiosResponse>) => Promise<unknown>
  triggerResponseError: (error: unknown) => Promise<unknown>
} {
  const requestHandlers: InterceptorHandler[] = []
  const responseHandlers: InterceptorHandler[] = []

  const api = {
    defaults: { withLoading: false as boolean | undefined },
    interceptors: {
      request: {
        use: vi.fn((onFulfilled: (r: unknown) => unknown, onRejected: (e: unknown) => unknown): void => {
          requestHandlers.push({ onFulfilled, onRejected })
        }),
      },
      response: {
        use: vi.fn((onFulfilled: (r: unknown) => unknown, onRejected: (e: unknown) => unknown): void => {
          responseHandlers.push({ onFulfilled, onRejected })
        }),
      },
    },
    triggerRequest: (config: Partial<InternalAxiosRequestConfig>): Promise<unknown> =>
      Promise.resolve(requestHandlers[0]?.onFulfilled(config)),
    triggerRequestError: (error: unknown): Promise<unknown> =>
      requestHandlers[0]?.onRejected(error) as Promise<unknown>,
    triggerResponse: (response: Partial<AxiosResponse>): Promise<unknown> =>
      Promise.resolve(responseHandlers[0]?.onFulfilled(response)),
    triggerResponseError: (error: unknown): Promise<unknown> =>
      responseHandlers[0]?.onRejected(error) as Promise<unknown>,
  }

  return api as unknown as ReturnType<typeof buildApi>
}

function listenForEvent(eventName: string): Promise<void> {
  return new Promise((resolve) => {
    document.addEventListener(eventName, () => resolve(), { once: true })
  })
}

describe('applyLoadingInterceptor()', () => {
  beforeEach(() => {
    vi.clearAllMocks()
  })

  it('should set api.defaults.withLoading to true', () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    expect(api.defaults.withLoading).toBe(true)
  })

  it('should dispatch loading:show on request when withLoading is true', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)

    const eventFired = listenForEvent(LOADING_SHOW)
    await api.triggerRequest({ withLoading: true } as InternalAxiosRequestConfig)

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should not dispatch loading:show when withLoading is false', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    api.defaults.withLoading = false

    let fired = false
    document.addEventListener(
      LOADING_SHOW,
      () => {
        fired = true
      },
      { once: true },
    )

    await api.triggerRequest({ withLoading: false } as InternalAxiosRequestConfig)

    expect(fired).toBe(false)
  })

  it('should dispatch loading:hide on request error', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)

    const eventFired = listenForEvent(LOADING_HIDE)
    await expect(api.triggerRequestError(new Error('fail'))).rejects.toThrow()

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should dispatch loading:hide on successful response', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)

    const eventFired = listenForEvent(LOADING_HIDE)
    await api.triggerResponse({
      config: { withLoading: true, persistLoading: false } as InternalAxiosRequestConfig,
    })

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should not dispatch loading:hide when persistLoading is true', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)

    let fired = false
    document.addEventListener(
      LOADING_HIDE,
      () => {
        fired = true
      },
      { once: true },
    )

    await api.triggerResponse({
      config: { withLoading: true, persistLoading: true } as InternalAxiosRequestConfig,
    })

    expect(fired).toBe(false)
  })

  it('should dispatch loading:hide on axios response error', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    mockedIsAxiosError.mockReturnValue(true)

    const eventFired = listenForEvent(LOADING_HIDE)
    const error = { config: { withLoading: true }, message: 'error' }

    await expect(api.triggerResponseError(error)).rejects.toBeDefined()

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should dispatch loading:hide on non-axios response error', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    mockedIsAxiosError.mockReturnValue(false)

    const eventFired = listenForEvent(LOADING_HIDE)

    await expect(api.triggerResponseError(new Error('unknown'))).rejects.toThrow()

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should dispatch loading:show when config.withLoading is undefined and defaults is true', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    api.defaults.withLoading = true

    const eventFired = listenForEvent(LOADING_SHOW)
    await api.triggerRequest({} as InternalAxiosRequestConfig)

    await expect(eventFired).resolves.toBeUndefined()
  })

  it('should not dispatch loading:hide on response when withLoading is false via defaults', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    api.defaults.withLoading = false

    let fired = false
    document.addEventListener(
      LOADING_HIDE,
      () => {
        fired = true
      },
      { once: true },
    )

    await api.triggerResponse({ config: {} as InternalAxiosRequestConfig })

    expect(fired).toBe(false)
  })

  it('should not dispatch loading:hide on axios error when withLoading is false via defaults', async () => {
    const api = buildApi()
    applyLoadingInterceptor(api)
    api.defaults.withLoading = false
    mockedIsAxiosError.mockReturnValue(true)

    let fired = false
    document.addEventListener(
      LOADING_HIDE,
      () => {
        fired = true
      },
      { once: true },
    )

    await expect(api.triggerResponseError({ config: {} })).rejects.toBeDefined()

    expect(fired).toBe(false)
  })
})
