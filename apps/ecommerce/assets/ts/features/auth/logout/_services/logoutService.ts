import api from '@/ts/core/http/api'
import { accessToken } from '@/ts/core/jwt/accessToken'

/**
 * Logs out via the JWT API endpoint.
 * Clears the in-memory access token; server clears the httpOnly refresh token cookie.
 */
export async function logout(): Promise<void> {
  try {
    await api.post('/api/auth/logout', null, { withCredentials: true, persistLoading: true })
  } finally {
    accessToken.clear()
  }
}
