export function attachViewportListener(onViewportChange: () => void): void {
  onViewportChange()
  window.addEventListener('resize', onViewportChange)
}
