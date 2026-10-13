import { getApiData } from '@/ts/core/http/_services/getApiData'

import type { SearchPage } from '@/ts/features/search/types'

/** Fetches a page of search results from the server */
export async function fetchSearchResults(term: string, signal?: AbortSignal, page = 1): Promise<SearchPage> {
const url = `/api/search?query=${encodeURIComponent(term)}&page=${page}&limit=10`
  const data = await getApiData(url, false, false, signal)
  const result = data as { html?: string; hasMore?: boolean } | null

  return {
    html: result?.html ?? '',
    hasMore: result?.hasMore ?? false,
  }
}
