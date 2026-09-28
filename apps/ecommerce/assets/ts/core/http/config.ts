import type { AxiosRequestConfig } from 'axios'

const axiosConfig: AxiosRequestConfig = {
  baseURL: '/',
  withCredentials: true,
  headers: {
    Accept: 'application/json',
  },
}

export default axiosConfig
