import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import { getCsrfToken } from '@/ts/shared/utils/dom/csrf'

import type { CartResponse } from '@/ts/features/cart/types'

type CartResult = Promise<CartResponse | null>

export async function cartAdd(variantId: string | number): CartResult {
  const formData = new FormData()
  formData.append('variant_id', String(variantId))
  formData.append('_csrf_token', getCsrfToken('csrf-cart-store'))

  return await submitFormData<CartResponse>('/cart/store', formData)
}

export async function cartRemove(itemId: string | number): CartResult {
  const formData = new FormData()
  formData.append('item_id', String(itemId))
  formData.append('_csrf_token', getCsrfToken('csrf-cart-destroy'))

  return await submitFormData<CartResponse>('/cart/destroy', formData)
}
