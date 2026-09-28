export function attachMouseEnterListener(star: HTMLImageElement, stars: HTMLImageElement[]): void {
  if (!star || !stars) return

  star.addEventListener('mouseenter', () => {
    const hoveredIndex = parseInt(star.dataset.index ?? '0', 10)

    stars.forEach((s) => {
      const idx = parseInt(s.dataset.index ?? '0', 10)
      s.classList.toggle('hovered', idx <= hoveredIndex)
    })
  })
}

export function attachMouseLeaveListener(star: HTMLImageElement, stars: HTMLImageElement[]): void {
  if (!star || !stars) return

  star.addEventListener('mouseleave', () => {
    stars.forEach((s) => s.classList.remove('hovered'))
  })
}
