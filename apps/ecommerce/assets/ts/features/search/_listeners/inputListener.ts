import type { SearchInstance } from '@/ts/features/search/types'
import * as inputHandler from '@/ts/features/search/_handlers/inputHandler'

export function attachInputChangeListener(
  input: HTMLInputElement,
  instance: SearchInstance,
  debouncedFetch: (value: string) => void,
  setSearchTerm: (val: string) => void,
): void {
  input.addEventListener('input', () => {
    inputHandler.handleSearchInputChange(input, instance, debouncedFetch, setSearchTerm)
  })
}

export function attachInputFocusListener(
  input: HTMLInputElement,
  instance: SearchInstance,
  setSearchTerm: (val: string) => void,
): void {
  input.addEventListener('focus', () => {
    inputHandler.handleSearchInputFocus(input, instance, setSearchTerm)
  })
}

export function attachInputKeyListener(input: HTMLInputElement, performSearch: () => void): void {
  input.addEventListener('keydown', (e) => {
    inputHandler.handleSearchInputKey(e, performSearch)
  })
}
