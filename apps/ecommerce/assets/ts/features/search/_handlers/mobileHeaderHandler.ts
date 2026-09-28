import type { SearchInstance } from '@/ts/features/search/types'
import { hasAnyText } from '@/ts/features/search/_utils/text'
import { updateSearch } from '@/ts/features/search/_ui/search'
import { isVisible, show } from '@/ts/features/search/_ui/visibility'

type MobileHeaderClickOptions = {
  instance: SearchInstance
  input: HTMLInputElement
}

export function handleMobileHeaderClick({ instance, input }: MobileHeaderClickOptions): void {
  if (!isVisible(instance.panel) && hasAnyText(input.value)) {
    instance.isClosed = false
    show(instance.panel)
    updateSearch(instance, instance.currentSearchTerm)
    input.focus()
  }
}
