export function hideAllNavItems(): void {
  document
    .querySelectorAll<HTMLElement>('.navigation__list-item.visible, .navigation__mobile-item.visible')
    .forEach((el) => el.classList.remove('visible'))
}

export function showNavItems(id: string): boolean {
  const desktopEl = document.getElementById(id)
  const mobileEl = document.getElementById(`mobile-${id}`)
  if (!desktopEl || !mobileEl) return false

  desktopEl.classList.add('visible')

  const mobileContainer = document.querySelector('.navigation__mobile')
  if (mobileContainer?.classList.contains('visible')) {
    mobileEl.classList.add('visible')
  }

  return true
}

export function showDesktopNavItem(id: string): boolean {
  const desktopEl = document.getElementById(id)
  if (!desktopEl) return false

  desktopEl.classList.add('visible')
  return true
}

export function showMobileNavItem(id: string): boolean {
  const mobileEl = document.getElementById(`mobile-${id}`)
  if (!mobileEl) return false

  mobileEl.classList.add('visible')
  return true
}
