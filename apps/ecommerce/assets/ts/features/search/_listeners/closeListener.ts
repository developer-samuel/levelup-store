import type { CloseHandlerOptions } from '@/ts/features/search/types'
import { handleSearchClear } from '@/ts/features/search/_handlers/closeHandler'

export function attachCloseListener(close: HTMLElement, handlerOptions: CloseHandlerOptions): void {
  close.addEventListener('click', () => {
    handleSearchClear(handlerOptions)
  })
}
