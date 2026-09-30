import axios, { type AxiosInstance, type InternalAxiosRequestConfig, type AxiosResponse } from 'axios'

import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

/** Shows loading indicator on request, hides it on request error */
function applyRequestInterceptor(api: AxiosInstance): void {
  api.interceptors.request.use(
    (config: InternalAxiosRequestConfig) => {
      if (config.withLoading ?? api.defaults.withLoading) {
        dispatchLoadingShow()
      }
      return config
    },
    (error: unknown) => {
      dispatchLoadingHide()
      return Promise.reject(error)
    },
  )
}

/** Hides loading indicator on response, hides it on response error */
function applyResponseInterceptor(api: AxiosInstance): void {
  api.interceptors.response.use(
    (response: AxiosResponse) => {
      if (!(response.config.withLoading ?? api.defaults.withLoading)) return response

      if (!response.config.persistLoading) {
        dispatchLoadingHide()
      }
      return response
    },
    (error: unknown) => {
      if (axios.isAxiosError(error)) {
        if (error.config?.withLoading ?? api.defaults.withLoading) {
          dispatchLoadingHide()
        }
      } else {
        dispatchLoadingHide()
      }
      return Promise.reject(error)
    },
  )
}

/** Adds request and response interceptors to show/hide a loading indicator */
export function applyLoadingInterceptor(api: AxiosInstance): void {
  api.defaults.withLoading = true

  applyRequestInterceptor(api)
  applyResponseInterceptor(api)
}
