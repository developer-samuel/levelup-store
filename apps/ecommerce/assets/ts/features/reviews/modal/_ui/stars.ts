export function updateStars(container: HTMLElement, rating: number): void {
  if (!container) return

  const stars = Array.from(container.querySelectorAll<HTMLImageElement>('img'))

  stars.forEach((s) => {
    const idx = parseInt(s.dataset.index ?? '0', 10)

    if (idx <= rating) {
      s.src = '/img/icons/elements/star/star.png'
      s.classList.add('active')
    } else {
      s.src = '/img/icons/elements/star/star-empty.png'
      s.classList.remove('active')
    }

    s.classList.remove('hovered')
  })
}
