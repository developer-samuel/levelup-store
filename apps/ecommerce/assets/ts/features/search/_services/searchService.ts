import { getApiData } from '@/ts/core/http/_services/getApiData'

/** Fetches search results as a rendered HTML string from the server */
export async function fetchSearchResults(term: string, signal?: AbortSignal): Promise<string> {
  const data = await getApiData(`/api/search?query=${encodeURIComponent(term)}`, false, false, signal)

  return (data as { html?: string } | null)?.html ?? ''
}
