import axios from 'axios'

import type { CartResponse } from '@/ts/features/cart/types'

export function isCartResponse(data: unknown): data is CartResponse {
  return typeof data === 'object' && data !== null && !Array.isArray(data) && 'success' in data
}

export function getCartErrorMessage(error: unknown): string {
  if (axios.isAxiosError(error) && isCartResponse(error.response?.data)) {
    return error.response?.data.message ?? 'Something went wrong.'
  }
  return 'Something went wrong.'
}
