import { getQueryParams } from '@/ts/shared/utils/query'

import type { SearchInstance } from '@/ts/features/search/types'
import { updateInstanceCloseIcons } from '@/ts/features/search/_ui/icons'
import { handleFetchSearch } from '@/ts/features/search/_handlers/fetchSearchHandler'
import { setSearchTermFlow } from '@/ts/features/search/_interactions/searchFlow'

export function bindSearchQuery(instance: SearchInstance): void {
  const query = getQueryParams('query')

  if (!query) return

  const isSearchPage = window.location.pathname.startsWith('/search/find')

  setSearchTermFlow(instance, query)
  updateInstanceCloseIcons(instance)

  handleFetchSearch(query, instance, !isSearchPage)
}
