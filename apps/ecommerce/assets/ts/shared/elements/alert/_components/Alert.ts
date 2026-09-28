import type { AlertOptions } from '@/ts/shared/elements/alert/types'
import { displayAlert, hideAlert } from '@/ts/shared/elements/alert/_interactions/control'
import { query } from '@/ts/shared/utils/dom/query'

type AlertInstance = {
  display(success: boolean, message: string, options?: AlertOptions): void
  clear(options?: AlertOptions): void
}

/**
 * Alert component.
 * Handles showing and clearing success/error messages.
 */
export default class Alert implements AlertInstance {
  private alert: HTMLElement
  private alertBody: HTMLElement

  constructor(alertSelector: string, alertBodySelector: string) {
    const alertEl = query<HTMLElement>(alertSelector)
    const alertBodyEl = query<HTMLElement>(alertBodySelector)

    if (!alertEl || !alertBodyEl) {
      throw new Error(`Alert elements not found for selectors: '${alertSelector}', '${alertBodySelector}'`)
    }

    this.alert = alertEl
    this.alertBody = alertBodyEl
  }

  display(success: boolean, message: string, options: AlertOptions = {}): void {
    displayAlert(this.alert, this.alertBody, message, success, options)
  }

  clear(options: AlertOptions = {}): void {
    hideAlert(this.alert, this.alertBody, options)
  }
}
