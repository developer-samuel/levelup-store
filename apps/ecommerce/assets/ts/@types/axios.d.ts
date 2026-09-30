import 'axios'

declare module 'axios' {
  interface AxiosRequestConfig {
    withLoading?: boolean
    persistLoading?: boolean
  }

  interface InternalAxiosRequestConfig {
    withLoading?: boolean
    persistLoading?: boolean
  }
}
