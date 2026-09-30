import { isAuth } from '@/ts/core/jwt/isAuth'
import { refreshToken } from '@/ts/core/jwt/_services/tokenRefreshService'

export async function refreshTokenGuard(): Promise<void> {
  if (!isAuth()) return

  await refreshToken()
}
