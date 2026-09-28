import type { DropdownInstance } from '@/ts/shared/elements/dropdown/types'
import { handleToggleClick } from '@/ts/shared/elements/dropdown/_handlers/toggleHandler'
import { handleDocumentClick } from '@/ts/shared/elements/dropdown/_handlers/documentHandler'
import { query } from '@/ts/shared/utils/dom/query'

export class Dropdown implements DropdownInstance {
  readonly toggle: HTMLElement | null
  readonly dropdown: HTMLElement | null

  constructor(toggleSelector: string, dropdownSelector: string) {
    this.toggle = query<HTMLElement>(toggleSelector)
    this.dropdown = query<HTMLElement>(dropdownSelector)

    if (this.toggle && this.dropdown) {
      this.toggle.addEventListener('click', (e) => handleToggleClick(e, this))
      this.dropdown.addEventListener('click', (e) => e.stopPropagation())
      document.addEventListener('click', (e) => handleDocumentClick(e, this))
    }
  }
}
