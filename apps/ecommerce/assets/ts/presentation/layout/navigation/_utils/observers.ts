type ObserverSet = {
  mutationObserver?: MutationObserver
  resizeObserver?: ResizeObserver
}

export function stopObservers(observers: ObserverSet = {}): void {
  observers.mutationObserver?.disconnect()
  observers.resizeObserver?.disconnect()
}
