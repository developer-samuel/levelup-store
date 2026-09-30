export function showCursorHover(element: Element): void {
  element.classList.add('cursor__hovered')
}

export function hideCursorHover(element: Element): void {
  element.classList.remove('cursor__hovered')
}

export function applyHoverIfPointer(targetEl: Element, cursorEl: Element): void {
  if (getComputedStyle(targetEl).cursor === 'pointer') {
    showCursorHover(cursorEl)
  }
}
