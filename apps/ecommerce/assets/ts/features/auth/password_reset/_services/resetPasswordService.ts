import { submitFormData } from '@/ts/core/http/_services/submitFormData'

import { query } from '@/ts/shared/utils/dom/query'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'

export default function resetPasswordSubmit(formData: FormData): FormSubmitResult {
  const tokenInput = query<HTMLInputElement>('input[name="token"]')
  const token = tokenInput?.value ?? null

  if (!token) return Promise.resolve(null)

  formData.set('token', token)

  return submitFormData<FormResponse>(`/reset-password/store?token=${encodeURIComponent(token)}`, formData, true, true)
}
