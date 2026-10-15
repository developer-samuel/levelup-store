export function show(panel: HTMLElement): void {
  panel.style.display = 'block'
  panel.classList.add('visible')
  document.body.classList.add('search--open')
}

export function hide(panel: HTMLElement): void {
  panel.style.display = 'none'
  panel.classList.remove('visible')
  document.body.classList.remove('search--open')
}

export function isVisible(panel: HTMLElement): boolean {
  return panel.classList.contains('visible')
}
