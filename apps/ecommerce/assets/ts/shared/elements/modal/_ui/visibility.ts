export function show(modal: HTMLElement, visibleClass: string): void {
  modal.classList.add(visibleClass)
  modal.setAttribute('aria-hidden', 'false')
}

export function hide(modal: HTMLElement, visibleClass: string): void {
  modal.classList.remove(visibleClass)
  modal.setAttribute('aria-hidden', 'true')
}
