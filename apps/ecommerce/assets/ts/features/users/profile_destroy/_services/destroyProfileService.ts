import { submitFormData } from '@/ts/core/http/_services/submitFormData'
import { getCsrfToken } from '@/ts/shared/utils/dom/csrf'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'

const CSRF_INPUT_ID = 'csrf-profile-destroy'

export default function destroyProfileSubmit(): FormSubmitResult {
  const formData = new FormData()
  formData.append('_csrf_token', getCsrfToken(CSRF_INPUT_ID))

  return submitFormData<FormResponse>('/profile/destroy', formData)
}
