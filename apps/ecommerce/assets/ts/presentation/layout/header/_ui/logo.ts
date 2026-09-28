import type { HeaderInstance } from '@/ts/presentation/layout/header/types'

const LOGO_BASE_URL = `${window.location.origin}/img/misc/logo`

const LOGO_DESKTOP_URL = `${LOGO_BASE_URL}/logo.webp`
const LOGO_MOBILE_URL = `${LOGO_BASE_URL}/logo-mini.webp`

export function updateLogo(instance: HeaderInstance, isDesktop: boolean): void {
  if (!instance.logoImage) return

  instance.logoImage.src = isDesktop ? LOGO_DESKTOP_URL : LOGO_MOBILE_URL
}
