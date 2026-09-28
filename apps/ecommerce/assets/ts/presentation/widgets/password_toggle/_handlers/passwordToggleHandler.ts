import { showPasswords, hidePasswords } from '@/ts/presentation/widgets/password_toggle/_ui/toggle'
import {
  resolveFields,
  resolveIcons,
  resolveIconForField,
  resolveFieldForIcon,
} from '@/ts/presentation/widgets/password_toggle/_ui/resolvers'

export function handlePasswordToggle(icon: HTMLElement): void {
  const field = resolveFieldForIcon(icon)
  const iconEl = resolveIconForField(icon)

  if (field) {
    const icons = iconEl ? [iconEl] : []

    if (field.type === 'text') {
      hidePasswords([field], icons)
    } else {
      showPasswords([field], icons)
    }

    return
  }

  const fields = resolveFields()
  if (fields.length === 0) return

  const firstField = fields[0]
  if (!firstField) return

  if (firstField.type === 'text') {
    hidePasswords(fields, resolveIcons())
  } else {
    showPasswords(fields, resolveIcons())
  }
}
