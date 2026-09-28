import { dispatchLoadingShow, dispatchLoadingHide } from '@/ts/shared/events/loading'

export function handleSearchRedirect(term: string): void {
  if (!term?.trim()) return

  try {
    dispatchLoadingShow()

    window.location.href = `/search/find?query=${encodeURIComponent(term)}`
  } finally {
    dispatchLoadingHide()
  }
}
