import { queryAll } from '@/ts/shared/utils/dom/query'

import {
  attachCursorMouseEnterListener,
  attachCursorMouseLeaveListener,
} from '@/ts/presentation/widgets/cursor/_listeners/mouseListener'

export function bindHoverEffects(cursorEl: Element): void {
  queryAll('*').forEach((el) => {
    attachCursorMouseEnterListener(el, cursorEl)
    attachCursorMouseLeaveListener(el, cursorEl)
  })
}
