import { queryAll } from '@/ts/shared/utils/dom/query'

import { TOGGLE_ICON_SELECTOR } from '@/ts/presentation/widgets/password_toggle/constants'
import { setCursorPointer } from '@/ts/presentation/widgets/password_toggle/_ui/cursor'
import { handlePasswordToggle } from '@/ts/presentation/widgets/password_toggle/_handlers/passwordToggleHandler'

export function bindPasswordToggle(): void {
  const icons = queryAll<HTMLElement>(TOGGLE_ICON_SELECTOR)
  if (!icons.length) return

  setCursorPointer(icons)

  for (const icon of icons) {
    icon.addEventListener('click', () => handlePasswordToggle(icon))
  }
}
