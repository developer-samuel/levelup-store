import type { AxiosError } from 'axios'

import type { StringListRecord } from '@/ts/shared/types'
import type { FormAlert, FormErrorsHandler, FormResponse } from '@/ts/shared/elements/form/types'
import { query } from '@/ts/shared/utils/dom/query'
import { scrollToContainer } from '@/ts/shared/utils/scroll'

type HttpError = {
  alert: FormAlert
  errors?: FormErrorsHandler
}

function scrollToError(errors: StringListRecord): void {
  const firstErrorField = Object.keys(errors)[0]
  if (!firstErrorField) return

  const el = query<HTMLElement>(`[name="${firstErrorField}"]`) ?? document.getElementById(firstErrorField)

  if (el) {
    el.scrollIntoView({ behavior: 'smooth', block: 'center' })
    el.focus()
  }
}

/**
 * Handle HTTP errors and display appropriate UI feedback.
 *
 * Supports validation errors, forbidden/too many requests, and generic errors.
 */
export function handleHttpError(
  error: AxiosError<FormResponse>,
  { alert, errors }: HttpError,
  shouldScroll = true,
): void {
  if (!error.response) return

  const { status, data } = error.response

  if (status === 422 && data.errors && errors) {
    errors.show(data.errors)
    if (shouldScroll) scrollToError(data.errors)
  } else {
    alert.display(false, data.message ?? 'An error occurred.')

    if (shouldScroll) scrollToContainer()
  }
}
