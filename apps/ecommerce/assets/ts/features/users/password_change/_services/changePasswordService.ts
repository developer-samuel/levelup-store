import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'

export default function changePasswordSubmit(formData: FormData): FormSubmitResult {
  return submitFormData<FormResponse>('/change-password/update', formData)
}
