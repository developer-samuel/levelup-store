import { getApiData } from '@/ts/core/http/_services/getApiData'

import type { UnknownRecord } from '@/ts/shared/types'

type DatatableQueryParams = {
  dataKey?: string
}

/** Fetches data for a datatable */
export async function fetchDatatableData(url: string, params: DatatableQueryParams = {}): Promise<unknown[]> {
  const data = await getApiData(url, true)
  if (!data) return []

  if (params.dataKey !== undefined && typeof data === 'object' && data !== null) {
    const items = (data as UnknownRecord)[params.dataKey]

    if (Array.isArray(items)) return items as unknown[]
  }

  if (Array.isArray(data)) return data as unknown[]

  return []
}
