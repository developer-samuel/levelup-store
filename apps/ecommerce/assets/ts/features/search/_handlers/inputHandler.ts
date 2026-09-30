import type { SearchInstance } from '@/ts/features/search/types'
import { LOADING_HTML, NO_RESULTS_HTML } from '@/ts/features/search/constants'
import { hasAnyText } from '@/ts/features/search/_utils/text'
import { updateContent } from '@/ts/features/search/_ui/content'
import { show, hide } from '@/ts/features/search/_ui/visibility'

export function handleSearchInputChange(
  input: HTMLInputElement,
  instance: SearchInstance,
  debouncedFetch: (value: string) => void,
  setSearchTerm: (val: string) => void,
): void {
  const value = input.value

  setSearchTerm(value)

  if (!value.trim()) {
    hide(instance.panel)
    updateContent(instance.content, NO_RESULTS_HTML)
    return
  }

  updateContent(instance.content, LOADING_HTML)
  show(instance.panel)
  instance.isClosed = false

  debouncedFetch(value)
}

export function handleSearchInputFocus(
  input: HTMLInputElement,
  instance: SearchInstance,
  setSearchTerm: (val: string) => void,
): void {
  const value = hasAnyText(input.value) ? input.value : instance.currentSearchTerm

  if (hasAnyText(value)) {
    if (input.value !== value) input.value = value
    instance.isClosed = false
    show(instance.panel)
    setSearchTerm(value)
  }
}

export function handleSearchInputKey(e: KeyboardEvent, performSearch: () => void): void {
  if (e.key === 'Enter') {
    e.preventDefault()
    performSearch()
  }
}
