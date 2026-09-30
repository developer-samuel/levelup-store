import api from '@/ts/core/http/api'

/** Performs a GET request and returns the response data, or 'null' on failure */
export async function getApiData(
  url: string,
  safe = true,
  withLoading = false,
  signal?: AbortSignal,
): Promise<unknown> {
  try {
    const response = await api.get<unknown>(url, {
      withLoading,
      ...(signal ? { signal } : {}),
    })
    return response.data ?? null
  } catch (error) {
    if (!safe) throw error
    return null
  }
}
