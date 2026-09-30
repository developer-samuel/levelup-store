import type { StringListRecord } from '@/ts/shared/types'

export function showErrors(
  form: HTMLFormElement | null,
  errorGroupClass: string | null,
  errors: StringListRecord,
): void {
  if (!form || !errorGroupClass) return

  for (const [field, messages] of Object.entries(errors)) {
    const input = form.querySelector(`[name="${field}"]`)
    if (!input) continue

    const errorDiv = input.closest(errorGroupClass)?.querySelector('.error')
    if (!errorDiv) continue

    const list = Array.isArray(messages) ? messages : [messages]

    errorDiv.textContent = ''
    list.forEach((msg, i) => {
      errorDiv.appendChild(document.createTextNode(msg))
      if (i < list.length - 1) errorDiv.appendChild(document.createElement('br'))
    })
  }
}

export function clearErrors(form: HTMLFormElement | null): void {
  if (!form) return

  form.querySelectorAll('.error').forEach((el) => (el.textContent = ''))
}
