import { query, queryAll } from '@/ts/shared/utils/dom/query'

import { TOGGLE_ICON_SELECTOR, PASSWORD_FIELDS_SELECTORS } from '@/ts/presentation/widgets/password_toggle/constants'

export function resolveFields(): HTMLInputElement[] {
  return PASSWORD_FIELDS_SELECTORS.map((selector) => query<HTMLInputElement>(selector)).filter(
    (el): el is HTMLInputElement => el !== null,
  )
}

export function resolveIcons(): HTMLImageElement[] {
  return Array.from(queryAll<HTMLImageElement>(TOGGLE_ICON_SELECTOR))
}

export function resolveIconForField(icon: HTMLElement): HTMLImageElement | null {
  return icon instanceof HTMLImageElement ? icon : null
}

export function resolveFieldForIcon(icon: HTMLElement): HTMLInputElement | null {
  return icon.parentElement?.querySelector<HTMLInputElement>('input') ?? null
}
