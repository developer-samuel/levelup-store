import axios from 'axios'

import { accessToken } from '@/ts/core/jwt/accessToken'

type RefreshQueueItem = (token: string) => void

let isRefreshing = false
let refreshQueue: RefreshQueueItem[] = []

function processRefreshQueue(token: string): void {
  refreshQueue.forEach((resolve) => resolve(token))
  refreshQueue = []
}

async function fetchAccessToken(): Promise<string> {
  const response = await axios.post<{ access_token?: string }>('/api/auth/refresh', null, {
    withCredentials: true,
  })

  const token = response.data?.access_token
  if (!token) throw new Error('Refresh failed: no access token in response')

  accessToken.set(token)

  return token
}

export async function refreshToken(): Promise<void> {
  if (isRefreshing) {
    return new Promise<void>((resolve) => {
      refreshQueue.push((_token: string) => resolve())
    })
  }

  isRefreshing = true

  try {
    const token = await fetchAccessToken()

    processRefreshQueue(token)
  } catch {
    refreshQueue = []
  } finally {
    isRefreshing = false
  }
}
