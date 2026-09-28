import { query } from '@/ts/shared/utils/dom/query'

import type { CursorInstance } from '@/ts/presentation/widgets/cursor/types'
import { CURSOR_SMOOTHNESS, CURSOR_DEFAULT_POSITION } from '@/ts/presentation/widgets/cursor/constants'
import { attachCursorMouseMoveListener } from '@/ts/presentation/widgets/cursor/_listeners/mouseListener'
import { setupAnimateCursor } from '@/ts/presentation/widgets/cursor/_interactions/animateCursor'
import { bindHoverEffects } from '@/ts/presentation/widgets/cursor/_interactions/hoverEffects'

export default class Cursor implements CursorInstance {
  readonly smoothness: number = CURSOR_SMOOTHNESS
  mouseX: number
  mouseY: number
  posX: number
  posY: number
  private readonly element: HTMLElement

  constructor(selector: string) {
    const el = query<HTMLElement>(selector)
    if (!el) throw new Error(`Cursor: No element found for selector '${selector}'`)

    this.element = el
    this.mouseX = CURSOR_DEFAULT_POSITION.x
    this.mouseY = CURSOR_DEFAULT_POSITION.y
    this.posX = this.mouseX
    this.posY = this.mouseY

    this.init()
  }

  private init(): void {
    bindHoverEffects(this.element)
    attachCursorMouseMoveListener(this)
    setupAnimateCursor(this, this.element)
  }
}
