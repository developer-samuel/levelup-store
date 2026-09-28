import type { CloseHandlerOptions } from '@/ts/features/search/types'

export function handleSearchClear({
  inputs,
  setSearchTerm,
  updateContent,
  hidePanel,
  updateUI,
  setIsClosed,
}: CloseHandlerOptions): void {
  inputs.forEach((input) => {
    input.value = ''
  })
  setSearchTerm('')
  updateContent('')
  hidePanel()
  updateUI()
  setIsClosed?.(true)
}
