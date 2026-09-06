import api from '@/ts/core/http/api'
import { accessToken } from '@/ts/core/jwt/accessToken'

import type { FormResponse, FormSubmitResult } from '@/ts/shared/elements/form/types'
import { dispatchLoadingHide } from '@/ts/shared/events/loading'

type LoginResponse = {
  status: 'success' | 'error'
  message: string
  data?: {
    access_token: string
    redirect: string
  }
}

/**
 * Logs in via the JWT API endpoint.
 * Stores the access token in memory, refresh token is set as httpOnly cookie by the server.
 */
async function login(email: string, password: string, cf_turnstile_response: string): Promise<LoginResponse> {
  const response = await api.post<LoginResponse>(
    '/api/auth/login',
    { email, password, cf_turnstile_response },
    { withCredentials: true, persistLoading: true },
  )

  const result = response.data

  if (result.status === 'success' && result.data?.access_token) {
    accessToken.set(result.data.access_token)
  } else {
    dispatchLoadingHide()
  }

  return result
}

export default async function loginSubmit(formData: FormData): FormSubmitResult {
  const email = formData.get('email') as string
  const password = formData.get('password') as string
  const cf_turnstile_response = document.querySelector<HTMLInputElement>('[name="cf-turnstile-response"]')?.value ?? ''

  const result = await login(email, password, cf_turnstile_response)

  const response: FormResponse = {
    success: result.status === 'success',
    message: result.message,
  }

  if (result.status === 'success') {
    response.redirect = result.data?.redirect ?? '/'
  }

  return response
}
