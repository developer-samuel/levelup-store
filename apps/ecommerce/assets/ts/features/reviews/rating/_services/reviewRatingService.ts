import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import type { RatingType } from '@/ts/features/reviews/rating/types'

export async function toggleReviewRating(reviewId: string | number, type: RatingType = 'like'): Promise<void> {
  const formData = new FormData()
  formData.append('reviewId', String(reviewId))

  if (type !== null) {
    formData.append('type', type)
  }

  await submitFormData('/reviews/ratings/toggle', formData, false, false, false)
}
