import { BREAKPOINT_SM, BREAKPOINT_LG, BREAKPOINT_XL } from '@/ts/shared/constants/breakpoints'

type ImageParts = {
  name: string
  ext: string
}

function extractImageParts(src: string): ImageParts {
  const extIndex = src.lastIndexOf('.')

  return {
    name: src.substring(0, extIndex),
    ext: src.substring(extIndex),
  }
}

function buildBannerFileName(name: string, size: string, ext: string): string {
  return `${name}-${size}${ext}`
}

export function resolveBannerSrc(originalSrc: string, width: number): string {
  const { name, ext } = extractImageParts(originalSrc)

  if (width < BREAKPOINT_XL && width >= BREAKPOINT_LG) return buildBannerFileName(name, '1280x500', ext)
  if (width < BREAKPOINT_LG && width >= BREAKPOINT_SM) return buildBannerFileName(name, '1024x650', ext)
  if (width < BREAKPOINT_SM) return buildBannerFileName(name, '640x560', ext)

  return `${name}${ext}`
}
