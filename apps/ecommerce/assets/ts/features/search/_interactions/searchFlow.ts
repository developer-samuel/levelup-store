import { handleFetchSearch } from '@/ts/features/search/_handlers/fetchSearchHandler'
import { handleSearchRedirect } from '@/ts/features/search/_handlers/redirectHandler'
import {
  attachInputChangeListener,
  attachInputFocusListener,
  attachInputKeyListener,
} from '@/ts/features/search/_listeners/inputListener'
import { attachCloseListener } from '@/ts/features/search/_listeners/closeListener'
import { updateSearch } from '@/ts/features/search/_ui/search'
import { updateContent } from '@/ts/features/search/_ui/content'
import { hide } from '@/ts/features/search/_ui/visibility'
import debounce from '@/ts/shared/utils/debounce'
import type { CloseHandlerOptions, SearchInstance } from '@/ts/features/search/types'

type BindSearchFlowOptions = {
  inputs: HTMLInputElement[]
  instance: SearchInstance
  closes: HTMLElement[]
  performSearch: () => void
  updateUI: (val?: string) => void
  setSearchTerm: (val: string) => void
}

export function setSearchTermFlow(instance: SearchInstance, val: string): void {
  if (val.trim().length === 0) {
    hide(instance.panel)
  }

  updateSearch(instance, val)
}

export function performSearchFlow(instance: SearchInstance): void {
  let term = instance.currentSearchTerm.trim()

  if (!term) {
    const firstInput = instance.inputs[0]
    const inputValue = firstInput?.value.trim()
    if (inputValue) {
      term = inputValue
      setSearchTermFlow(instance, inputValue)
    }
  }

  if (term) {
    handleSearchRedirect(term)
  }
}

export function bindSearchFlow({
  inputs,
  instance,
  closes,
  performSearch,
  updateUI,
  setSearchTerm,
}: BindSearchFlowOptions): void {
  const debouncedFetch = debounce((value: string) => handleFetchSearch(value, instance), 750)

  inputs.forEach((input) => {
    attachInputChangeListener(input, instance, debouncedFetch, setSearchTerm)
    attachInputFocusListener(input, instance, setSearchTerm)
    attachInputKeyListener(input, performSearch)
  })

  const handlerOptions: CloseHandlerOptions = {
    inputs,
    setSearchTerm,
    updateContent: (html: string): void => updateContent(instance.content, html),
    hidePanel: (): void => {
      instance.isClosed = true
      hide(instance.panel)
    },
    updateUI,
    setIsClosed: (val: boolean): void => {
      instance.isClosed = val
    },
  }

  closes.forEach((close) => {
    attachCloseListener(close, handlerOptions)
  })
}
