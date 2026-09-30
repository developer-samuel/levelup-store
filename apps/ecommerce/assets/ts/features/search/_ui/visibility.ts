export function show(panel: HTMLElement): void {
  panel.style.display = 'block'
  panel.classList.add('visible')
}

export function hide(panel: HTMLElement): void {
  panel.style.display = 'none'
  panel.classList.remove('visible')
}

export function isVisible(panel: HTMLElement): boolean {
  return panel.classList.contains('visible')
}
