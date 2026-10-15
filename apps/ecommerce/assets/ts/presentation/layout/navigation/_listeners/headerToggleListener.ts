import { HEADER_TOGGLE } from '@/ts/presentation/layout/header/_events/toggle'
import { handleHeaderToggle } from '@/ts/presentation/layout/navigation/_handlers/navHandler'

export function attachHeaderToggleListener(
  mobileContainer: HTMLElement | null,
  navContainer: HTMLElement | null,
): void {
  const isHidden = document.querySelector('.header')?.classList.contains('header--hidden') ?? false
  handleHeaderToggle(isHidden, mobileContainer, navContainer)

  document.addEventListener(HEADER_TOGGLE, (e) => {
    const { hidden } = (e as CustomEvent<{ hidden: boolean }>).detail
    handleHeaderToggle(hidden, mobileContainer, navContainer)
  })
}
