import type { FormAlert, FormErrorsHandler, FormSubmitHandler } from '@/ts/shared/elements/form/types'

/**
 * Attaches a submit listener to a form.
 * Clears previous errors and delegates handling to the provided submit function.
 */
export function attachSubmitListener(
  form: HTMLFormElement | null,
  submitHandler: FormSubmitHandler,
  errors: FormErrorsHandler,
  alert: FormAlert,
): void {
  if (!form || !submitHandler) return

  let isSubmitting = false

  form.addEventListener('submit', (e: SubmitEvent) => {
    e.preventDefault()

    if (isSubmitting) return
    isSubmitting = true

    if (errors?.clear) errors.clear()

    void submitHandler(form, alert, errors)
      .finally(() => {
        isSubmitting = false
      })
      .catch(() => {})
  })
}
