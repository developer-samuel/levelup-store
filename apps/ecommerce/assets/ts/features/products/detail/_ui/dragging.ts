export function applyDraggingStyles(track: HTMLElement): void {
  track.style.transition = 'none'
  track.classList.add('grabbing')
}

export function removeDraggingStyles(track: HTMLElement): void {
  track.style.transition = ''
  track.classList.remove('grabbing')
}
