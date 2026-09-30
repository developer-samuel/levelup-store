import { attachDestroyListener } from '@/ts/shared/elements/actions/_listeners/destroyListener'

import { handleDeleteClick } from '@/ts/features/reviews/list/_handlers/reviewDestroyHandler'

export default class ReviewList {
  constructor() {
    attachDestroyListener('.reviews__card-item-destroy', handleDeleteClick)
  }
}
