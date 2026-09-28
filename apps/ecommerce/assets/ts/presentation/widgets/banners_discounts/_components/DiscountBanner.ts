import { createBannerState } from '@/ts/presentation/widgets/banners_discounts/_state/banner'
import { bindUpdateBanner } from '@/ts/presentation/widgets/banners_discounts/_interactions/updateBanner'

export default class DiscountBanner {
  constructor(id: string) {
    const el = document.getElementById(id)
    if (!(el instanceof HTMLImageElement)) return

    this.init(el)
  }

  private init(bannerImg: HTMLImageElement): void {
    const { bannerImg: img, originalSrc } = createBannerState(bannerImg)

    bindUpdateBanner(img, originalSrc)
  }
}
