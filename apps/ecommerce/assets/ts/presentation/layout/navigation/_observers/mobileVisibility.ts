type MobileVisibilityObservers = {
  mutationObserver: MutationObserver
  resizeObserver: ResizeObserver
}

function createMutationObserver(callback: MutationCallback): MutationObserver {
  return new MutationObserver(callback)
}

function createResizeObserver(callback: ResizeObserverCallback): ResizeObserver {
  return new ResizeObserver(callback)
}

export function observeMobileVisibility(
  mobileContainer: HTMLElement | null,
  callback: () => void,
): MobileVisibilityObservers | null {
  if (!mobileContainer) return null

  const mutationObserver = createMutationObserver(callback)
  const resizeObserver = createResizeObserver(callback)

  mutationObserver.observe(mobileContainer, {
    attributes: true,
    attributeFilter: ['class'],
  })

  resizeObserver.observe(mobileContainer)

  return { mutationObserver, resizeObserver }
}
