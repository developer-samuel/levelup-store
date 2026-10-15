import { applyHeaderStateOnLoad } from '@/ts/presentation/layout/header/_handlers/scrollHandler'

export function applyScrollVisibility(headerMain: HTMLElement | null): void {
  if (!headerMain) return

  applyHeaderStateOnLoad(headerMain)
}
