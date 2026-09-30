import type { SearchInstance } from '@/ts/features/search/types'
import { hasInputValue } from '@/ts/features/search/_utils/input'

function updateToggleIcons(closes: HTMLElement[], icons: HTMLElement[], shouldShowClose: boolean): void {
  closes.forEach((close) => {
    close.style.display = shouldShowClose ? 'flex' : 'none'
  })

  icons.forEach((icon) => {
    icon.style.display = shouldShowClose ? 'none' : 'flex'
  })
}

function updateCloseImages(
  mobileCloseImage: HTMLElement | null,
  headerCloseImage: HTMLElement | null,
  shouldShowClose: boolean,
): void {
  if (mobileCloseImage) {
    mobileCloseImage.style.display = shouldShowClose ? 'flex' : 'none'
  }

  if (headerCloseImage) {
    headerCloseImage.style.display = shouldShowClose ? 'flex' : 'none'
  }
}

export function updateInstanceCloseIcons(instance: SearchInstance): void {
  const shouldShowClose = hasInputValue(instance)

  updateToggleIcons(instance.closes, instance.icons, shouldShowClose)
  updateCloseImages(instance.mobileCloseImage, instance.headerCloseImage, shouldShowClose)
}
