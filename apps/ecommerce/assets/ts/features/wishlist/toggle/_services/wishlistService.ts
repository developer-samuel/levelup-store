import { submitFormData } from '@/ts/core/http/_services/submitFormData'

export async function wishlistToggle(variantId: string): Promise<void> {
  const formData = new FormData()
  formData.append('variant_id', variantId)

  await submitFormData('/wishlist/toggle', formData, false)
}
