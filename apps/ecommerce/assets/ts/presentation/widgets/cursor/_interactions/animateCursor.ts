import type { CursorInstance } from '@/ts/presentation/widgets/cursor/types'
import { calculatePosition } from '@/ts/presentation/widgets/cursor/_utils/calculatePosition'

function bindAnimateCursor(ctx: CursorInstance, element: HTMLElement): void {
  const newPosX = calculatePosition(ctx.posX, ctx.mouseX, ctx.smoothness)
  const newPosY = calculatePosition(ctx.posY, ctx.mouseY, ctx.smoothness)

  ctx.posX = newPosX
  ctx.posY = newPosY

  element.style.left = `${newPosX}px`
  element.style.top = `${newPosY}px`
}

/** Starts the cursor animation loop via requestAnimationFrame */
export function setupAnimateCursor(ctx: CursorInstance, element: HTMLElement): void {
  function animate(): void {
    bindAnimateCursor(ctx, element)
    requestAnimationFrame(animate)
  }

  animate()
}
