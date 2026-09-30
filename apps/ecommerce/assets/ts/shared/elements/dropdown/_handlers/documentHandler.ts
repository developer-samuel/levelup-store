import type { DropdownInstance } from '@/ts/shared/elements/dropdown/types'
import { isClickInside } from '@/ts/shared/elements/dropdown/_utils/clickTarget'

/**
 * Close dropdown if click happens outside of it.
 */
export function handleDocumentClick(e: MouseEvent, dropdownInstance: DropdownInstance): void {
  if (!dropdownInstance.toggle || !dropdownInstance.dropdown) return

  if (!isClickInside(dropdownInstance.toggle, dropdownInstance.dropdown, e.target)) {
    dropdownInstance.dropdown.classList.remove('visible')
  }
}
