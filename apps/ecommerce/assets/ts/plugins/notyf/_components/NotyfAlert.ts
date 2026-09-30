import { Notyf } from 'notyf'

import 'notyf/notyf.min.css'

import NOTYF_CONFIG from '@/ts/plugins/notyf/config'

type NotyfAlertInstance = {
  success(msg: string): void
  error(msg: string): void
  info(msg: string): void
}

/**
 * NotyfAlert singleton component.
 * Provides typed methods for success, error, and info notifications.
 */
class NotyfAlert implements NotyfAlertInstance {
  private readonly notyf: Notyf

  constructor() {
    this.notyf = new Notyf(NOTYF_CONFIG)
  }

  /**
   * Show success notification.
   */
  success(msg: string): void {
    this.notyf.success(msg)
  }

  /**
   * Show error notification.
   */
  error(msg: string): void {
    this.notyf.error(msg)
  }

  /**
   * Show info notification.
   */
  info(msg: string): void {
    this.notyf.open({ type: 'info', message: msg })
  }
}

const notyfAlert = new NotyfAlert()

export default notyfAlert
