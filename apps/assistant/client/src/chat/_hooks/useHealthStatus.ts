import { useEffect, useState } from 'react'

import { API_BASE_URL, API_KEY } from '@/app/api.config'

export type HealthStatus = 'loading' | 'online' | 'offline'

export function useHealthStatus(): HealthStatus {
  const [status, setStatus] = useState<HealthStatus>('loading')

  useEffect(() => {
    let cancelled = false
    let retryTimer: ReturnType<typeof setTimeout> | null = null

    async function check() {
      try {
        const res = await fetch(`${API_BASE_URL}/health`, {
          headers: API_KEY ? { 'X-Api-Key': API_KEY } : {},
        })

        if (cancelled) return

        if (res.ok) {
          setStatus('online')
        } else {
          setStatus('offline')
          retryTimer = setTimeout(() => {
            void check()
          }, 3_000)
        }
      } catch {
        if (cancelled) return

        setStatus('offline')
        retryTimer = setTimeout(() => {
          void check()
        }, 3_000)
      }
    }

    void check()

    return () => {
      cancelled = true

      if (retryTimer) clearTimeout(retryTimer)
    }
  }, [])

  return status
}
