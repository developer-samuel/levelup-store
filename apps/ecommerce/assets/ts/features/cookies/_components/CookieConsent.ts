import { query } from '@/ts/shared/utils/dom/query'

import { handleAcceptCookies } from '@/ts/features/cookies/_handlers/cookieHandler'

export default class CookieConsent {
  private readonly cookieModal: HTMLElement | null
  private readonly acceptButton: HTMLElement | null

  constructor(modalSelector: string, acceptButtonSelector: string) {
    this.cookieModal = query<HTMLElement>(modalSelector)
    this.acceptButton = query<HTMLElement>(acceptButtonSelector)

    this.acceptButton?.addEventListener('click', () => void handleAcceptCookies(this.cookieModal))
  }
}
