import { trapTab } from '@/ts/features/reviews/modal/_ui/focus'

/** Handles modal keyboard navigation - Escape closes, Tab traps focus */
export function handleKeydown(
  e: KeyboardEvent,
  modal: HTMLElement,
  close: () => void,
  focusableSelector: string,
): void {
  if (e.key === 'Escape') return close()
  if (e.key === 'Tab') trapTab(e, modal, focusableSelector)
}
