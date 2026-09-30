import { toggleClass } from '@/ts/shared/utils/dom/classes'

const FILTER_HIDDEN = 'products__filter-mobile--header-hidden'
const CARD_OPTIONS_HIDDEN = 'products__card-options--header-hidden'

export function handleStickyOffsetToggle(
  event: Event,
  filterMobile: HTMLElement | null,
  cardOptions: HTMLElement | null,
): void {
  const { hidden } = (event as CustomEvent<{ hidden: boolean }>).detail

  toggleClass(filterMobile, FILTER_HIDDEN, hidden)
  toggleClass(cardOptions, CARD_OPTIONS_HIDDEN, hidden)
}
