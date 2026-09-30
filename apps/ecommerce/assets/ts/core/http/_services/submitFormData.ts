import api from '@/ts/core/http/api'

import type { StringRecord, UnknownRecord } from '@/ts/shared/types'

const pendingRequests = new Set<string>()

/** Submits form data or a JSON-serialisable object to a given URL via POST */
export async function submitFormData<T = unknown>(
  url: string,
  formData: FormData | UnknownRecord | null = null,
  withLoading = true,
  persistLoading = false,
  checkSubmitting = true,
): Promise<T | null> {
  if (checkSubmitting && pendingRequests.has(url)) return null
  if (checkSubmitting) pendingRequests.add(url)

  try {
    const headers: StringRecord = formData instanceof FormData ? {} : { 'Content-Type': 'application/json' }

    const response = await api.post<T>(url, formData, {
      headers,
      withLoading,
      persistLoading,
    })

    return response.data
  } finally {
    pendingRequests.delete(url)
  }
}
