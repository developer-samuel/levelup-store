import type { RatingState } from '@/ts/features/reviews/modal/types'

export function attachClickListener(
  star: HTMLImageElement,
  stars: HTMLImageElement[],
  index: number,
  container: HTMLElement,
  updateStarsFn: (container: HTMLElement, rating: number) => void,
  ratingState: RatingState,
  hiddenValueInput: HTMLInputElement | null,
): void {
  if (!star || !stars || !container || !ratingState) return

  star.addEventListener('click', () => {
    ratingState.value = ratingState.value === index ? 0 : index
    container.dataset.value = ratingState.value ? String(ratingState.value) : ''

    if (hiddenValueInput) {
      hiddenValueInput.value = ratingState.value ? String(ratingState.value) : ''
    }

    updateStarsFn(container, ratingState.value)
  })
}
