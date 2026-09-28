export function findClosestIcon(event: MouseEvent): HTMLElement | null {
  return event.target instanceof Element
    ? event.target.closest<HTMLElement>('.reviews__card-item-row-right-rating-icon')
    : null
}

export function findClickableParent(icon: HTMLElement, container: HTMLElement): HTMLElement | null {
  const clicked = icon.closest<HTMLElement>('#reviews-like, #reviews-dislike')

  if (!clicked || !container.contains(clicked)) return null

  return clicked
}

export function getParentRow(clicked: HTMLElement): HTMLElement | null {
  return clicked.closest<HTMLElement>('.reviews__card-item-row-right')
}
