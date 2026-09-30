import type { DropdownInstance } from '@/ts/shared/elements/dropdown/types'

/**
 * Toggle dropdown visibility on click.
 */
export function handleToggleClick(e: MouseEvent, dropdownInstance: DropdownInstance): void {
  e.stopPropagation()

  if (!dropdownInstance.dropdown) return

  dropdownInstance.dropdown.classList.toggle('visible')
}
