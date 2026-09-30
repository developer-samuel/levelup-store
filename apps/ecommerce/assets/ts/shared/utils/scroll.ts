function getScrollContainer(): Element {
  const elements = [document.documentElement, document.body, ...Array.from(document.querySelectorAll('*'))]

  for (const el of elements) {
    if (el.scrollTop > 0) return el
  }

  return document.documentElement
}

export function scrollToTop(): void {
  window.scrollTo({ top: 0, behavior: 'smooth' })
}
export function scrollToContainer(): void {
  getScrollContainer().scrollTo({ top: 0, behavior: 'smooth' })
}
