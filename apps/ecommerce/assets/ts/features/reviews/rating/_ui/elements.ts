export function getRatingElements(container: HTMLElement): {
  likeEl: HTMLElement | null
  dislikeEl: HTMLElement | null
} {
  return {
    likeEl: container.querySelector<HTMLElement>('#reviews-like'),
    dislikeEl: container.querySelector<HTMLElement>('#reviews-dislike'),
  }
}
