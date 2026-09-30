type BannerState = {
  bannerImg: HTMLImageElement
  originalSrc: string
  updateBanner?: () => void
}

export function createBannerState(bannerImg: HTMLImageElement): BannerState {
  return {
    bannerImg,
    originalSrc: bannerImg.getAttribute('src') ?? bannerImg.src,
  }
}
