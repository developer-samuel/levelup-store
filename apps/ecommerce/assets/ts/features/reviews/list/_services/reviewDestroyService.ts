import { submitFormData } from '@/ts/core/http/_services/submitFormData'

export async function reviewDestroy(reviewId: string | number): Promise<void> {
  const formData = new FormData()
  formData.append('reviewId', String(reviewId))

  await submitFormData('/reviews/destroy', formData, true, true)
}
