import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'

export default function orderStatusSubmit(formData: FormData): FormSubmitResult {
  const urlParts = window.location.pathname.split('/')
  const code = urlParts[urlParts.length - 1]

  return submitFormData<FormResponse>(`/admin/orders/status/update?code=${code}`, formData)
}
