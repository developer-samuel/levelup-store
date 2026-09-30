import type { SearchInstance } from '@/ts/features/search/types'
import { syncInputValues } from '@/ts/features/search/_utils/input'
import { updateInstanceCloseIcons } from '@/ts/features/search/_ui/icons'
import { updateSearchUI } from '@/ts/features/search/_ui/panel'

export function updateSearch(instance: SearchInstance, value: string): void {
  instance.currentSearchTerm = value

  syncInputValues(instance.inputs, value)

  updateSearchUI(instance)
  updateInstanceCloseIcons(instance)
}
