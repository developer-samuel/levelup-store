import { attachToggleListener } from '@/ts/shared/elements/actions/_listeners/toggleListener'

import { syncRatingHighlight } from '@/ts/features/reviews/rating/_ui/rating'
import { handleRatingToggle } from '@/ts/features/reviews/rating/_handlers/reviewRatingHandler'

export function bindToggle(selector: string): void {
  attachToggleListener(selector, (el) => ({ id: el.dataset.reviewId }), handleRatingToggle, 200, syncRatingHighlight)
}
