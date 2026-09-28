import type { CursorInstance } from '@/ts/presentation/widgets/cursor/types'
import { applyHoverIfPointer, hideCursorHover } from '@/ts/presentation/widgets/cursor/_ui/hover'

export function attachCursorMouseEnterListener(el: Element, cursorEl: Element): void {
  el.addEventListener('mouseenter', () => applyHoverIfPointer(el, cursorEl))
}

export function attachCursorMouseLeaveListener(el: Element, cursorEl: Element): void {
  el.addEventListener('mouseleave', () => hideCursorHover(cursorEl))
}

export function attachCursorMouseMoveListener(ctx: CursorInstance): void {
  document.addEventListener('mousemove', (e) => {
    ctx.mouseX = e.clientX
    ctx.mouseY = e.clientY
  })
}
