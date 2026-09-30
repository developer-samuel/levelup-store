import { submitFormData } from '@/ts/core/http/_services/submitFormData'

/** Sends a delete request for a specific resource by ID */
export async function destroyData<T = unknown>(url: string, id: string | number): Promise<T | null> {
  const formData = new FormData()
  formData.append('id', String(id))

  return submitFormData<T>(url, formData)
}
