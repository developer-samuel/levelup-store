import axios, { type AxiosInstance, type AxiosResponse } from 'axios'

import { logDevError } from '@/ts/shared/utils/logger'

/** Logs non-cancelled HTTP errors in development */
export function applyErrorInterceptor(api: AxiosInstance): void {
  api.interceptors.response.use(
    (response: AxiosResponse) => response,
    (error: unknown) => {
      if (!axios.isCancel(error)) {
        logDevError('[API]', error)
      }
      return Promise.reject(error)
    },
  )
}
