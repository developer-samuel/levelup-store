import { logDevError } from '@/ts/shared/utils/logger'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

import { storeCookieConsent } from '@/ts/features/cookies/_services/cookieService'

export async function handleAcceptCookies(modal: HTMLElement | null): Promise<void> {
  try {
    const data = await storeCookieConsent()

    NotyfAlert.success(data.message)

    if (modal) {
      modal.style.display = 'none'
      modal.classList.remove('visible')
    }
  } catch (error) {
    logDevError('[Cookie]', error)
    NotyfAlert.error('Something went wrong. Please try again.')
  }
}
