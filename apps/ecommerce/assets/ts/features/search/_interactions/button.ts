import type { CloseHandlerOptions } from '@/ts/features/search/types'
import { attachCloseListener } from '@/ts/features/search/_listeners/closeListener'

type BindButtonOptions = {
  searchButton: HTMLElement | null
  mobileSearchButton: HTMLElement | null
  userButton: HTMLElement | null
  clearElements: Array<HTMLElement | null>
  performSearch: () => void
  clearSearchInputs: CloseHandlerOptions
  hidePanel: () => void
}

export function setupButton({
  searchButton,
  mobileSearchButton,
  userButton,
  clearElements,
  performSearch,
  clearSearchInputs,
  hidePanel,
}: BindButtonOptions): void {
  searchButton?.addEventListener('click', performSearch)
  mobileSearchButton?.addEventListener('click', performSearch)
  userButton?.addEventListener('click', hidePanel)

  clearElements
    .filter((el): el is HTMLElement => el !== null)
    .forEach((el) => {
      attachCloseListener(el, clearSearchInputs)
    })
}
