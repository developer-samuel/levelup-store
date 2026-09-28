import { bindToggle } from '@/ts/features/reviews/rating/_interactions/toggle'

export default class ReviewRating {
  constructor(selector: string) {
    bindToggle(selector)
  }
}
