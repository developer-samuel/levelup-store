type TableClickOptions = {
  hrefContains?: string[]
  confirmMessage?: string
  onClick?: (el: HTMLElement) => void | Promise<void>
  selector?: string
}

/**
 * Generic click handler for table buttons or links.
 * Prevents default click unless the element's href contains specified strings.
 */
export function handleTableClick(
  e: Event,
  { hrefContains, confirmMessage, onClick, selector }: TableClickOptions,
): void {
  const target = e.target instanceof HTMLElement ? e.target : null
  if (!target) return

  const el = selector ? target.closest<HTMLElement>(selector) : (target.closest<HTMLElement>('a, button') ?? target)
  if (!el) return

  // Allow default action if href contains any of the specified strings
  if (hrefContains && el instanceof HTMLAnchorElement) {
    if (hrefContains.some((str) => el.href.includes(str))) return
  }

  // Only prevent default when there is something to handle
  if (!onClick && !confirmMessage) return

  e.preventDefault()

  // Show confirmation dialog if message provided
  if (confirmMessage && !window.confirm(confirmMessage)) {
    return
  }

  if (typeof onClick === 'function') {
    void onClick(el)
  }
}
