import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'

export default function forgotPasswordSubmit(formData: FormData): FormSubmitResult {
  return submitFormData<FormResponse>('/forgot-password/store', formData)
}
