import type { TimeoutId } from '@/ts/shared/types'
import { query } from '@/ts/shared/utils/dom/query'

let activeRequests = 0
let hideTimeoutId: TimeoutId | null = null

export function showLoading(): void {
  const loading = query<HTMLElement>('.loading')
  if (!loading) return

  if (document.activeElement instanceof HTMLElement) {
    document.activeElement.blur()
  }

  activeRequests++

  if (hideTimeoutId !== null) {
    clearTimeout(hideTimeoutId)
    hideTimeoutId = null
  }

  if (activeRequests === 1) {
    loading.classList.add('loading--action')
    loading.classList.remove('hidden')
  }
}

export function hideLoading(): void {
  const loading = query<HTMLElement>('.loading')
  if (!loading) return

  activeRequests = Math.max(0, activeRequests - 1)

  if (activeRequests === 0) {
    loading.classList.add('hidden')

    hideTimeoutId = setTimeout(() => {
      if (activeRequests === 0) {
        loading.classList.remove('loading--action')
      }
      hideTimeoutId = null
    }, 150)
  }
}
