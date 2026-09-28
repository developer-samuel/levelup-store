import { resolveBannerSrc } from '@/ts/presentation/widgets/banners_discounts/_utils/bannerSrc'
import { attachViewportListener } from '@/ts/presentation/widgets/banners_discounts/_listeners/viewportListener'

export function bindUpdateBanner(bannerImg: HTMLImageElement, originalSrc: string): void {
  attachViewportListener(() => {
    bannerImg.src = resolveBannerSrc(originalSrc, window.innerWidth)
  })
}
