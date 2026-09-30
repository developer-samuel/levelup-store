import api from '@/ts/core/http/api'

type CookieConsentResponse = {
  success: boolean
  message: string
}

export async function storeCookieConsent(): Promise<CookieConsentResponse> {
  const response = await api.post<CookieConsentResponse>('/api/cookies/store')

  return response.data
}
