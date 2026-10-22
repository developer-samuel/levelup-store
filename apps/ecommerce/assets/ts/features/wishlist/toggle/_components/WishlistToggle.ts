import { bindToggle } from '@/ts/features/wishlist/toggle/_interactions/toggle'

export default class WishlistToggle {
  constructor(selector: string) {
    bindToggle(selector)
  }

  static init(): void {
    new WishlistToggle('.product-detail__details-wishlist')
  }
}
