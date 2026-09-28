import type { TimeoutId } from '@/ts/shared/types'
import type { AlertOptions } from '@/ts/shared/elements/alert/types'
import { ALERT_CONFIG } from '@/ts/shared/elements/alert/config'
import { show, hide } from '@/ts/shared/elements/alert/_ui/visibility'

let timeoutId: TimeoutId | undefined

/** Show alert with optional success/error styling and auto-hide */
export function displayAlert(
  alertEl: HTMLElement,
  alertBodyEl: HTMLElement,
  message: string,
  success: boolean,
  options: AlertOptions = {},
): void {
  if (!alertEl || !alertBodyEl) return

  const { successDuration, successClass, errorClass } = { ...ALERT_CONFIG, ...options }

  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = undefined
  }

  show(alertEl, alertBodyEl, message, successClass, errorClass, success)

  if (success) {
    timeoutId = setTimeout(() => {
      hide(alertEl, alertBodyEl, successClass, errorClass)
      timeoutId = undefined
    }, successDuration)
  }
}

/** Hide alert immediately */
export function hideAlert(
  alertEl: HTMLElement,
  alertBodyEl: HTMLElement,
  { successClass = 'alert--success', errorClass = 'alert--error' }: AlertOptions = {},
): void {
  if (!alertEl || !alertBodyEl) return

  if (timeoutId) {
    clearTimeout(timeoutId)
    timeoutId = undefined
  }

  hide(alertEl, alertBodyEl, successClass, errorClass)
}
