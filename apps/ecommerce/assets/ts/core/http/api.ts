import axios, { type AxiosInstance } from 'axios'

import axiosConfig from '@/ts/core/http/config'
import { applyErrorInterceptor } from '@/ts/core/http/_interceptors/error.interceptor'
import { applyLoadingInterceptor } from '@/ts/core/http/_interceptors/loading.interceptor'

const api: AxiosInstance = axios.create(axiosConfig)

applyLoadingInterceptor(api)
applyErrorInterceptor(api)

export default api
