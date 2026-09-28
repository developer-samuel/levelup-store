import type { SearchInstance } from '@/ts/features/search/types'
import { LOADING_HTML, ERROR_HTML, NO_RESULTS_HTML } from '@/ts/features/search/constants'
import { updateContent } from '@/ts/features/search/_ui/content'
import { renderFetchResults } from '@/ts/features/search/_ui/fetchResults'
import { hide } from '@/ts/features/search/_ui/visibility'
import { getAbortSignal } from '@/ts/features/search/_state/abortSignal'
import { fetchSearchResults } from '@/ts/features/search/_services/searchService'

export function handleFetchSearch(term: string, instance: SearchInstance, showPanel = true): void {
  const signal = getAbortSignal()

  if (!term.trim()) {
    hide(instance.panel)
    updateContent(instance.content, NO_RESULTS_HTML)
    return
  }

  void renderFetchResults(instance, (sig) => fetchSearchResults(term, sig), {
    loadingHtml: LOADING_HTML,
    noResultsHtml: NO_RESULTS_HTML,
    errorHtml: ERROR_HTML,
    signal,
    showPanel,
  })
}
