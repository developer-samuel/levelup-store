import axios from 'axios'

import { logDevError } from '@/ts/shared/utils/logger'

import type { SearchInstance, RenderFetchOptions, SearchPage } from '@/ts/features/search/types'
import { updateContent, appendContent } from '@/ts/features/search/_ui/content'
import { show } from '@/ts/features/search/_ui/visibility'
import { handleFetchSearch } from '@/ts/features/search/_handlers/fetchSearchHandler'

type NullableSearchPage = Promise<SearchPage | null | undefined>

export async function renderFetchResults(
  instance: SearchInstance,
  fetchFn: (signal?: AbortSignal) => NullableSearchPage,
  { loadingHtml, noResultsHtml, errorHtml, signal, showPanel = true, page = 1 }: RenderFetchOptions,
): Promise<void> {
  if (instance.isClosed) return

  if (showPanel && page === 1) {
    updateContent(instance.content, loadingHtml)
    show(instance.panel)
  }

  try {
    const result = await fetchFn(signal)

    if (instance.isClosed) return

    const html = result?.html?.trim() ?? ''
    instance.hasMore = result?.hasMore ?? false

    if (!html) {
      if (page === 1) updateContent(instance.content, noResultsHtml)
      return
    }

    if (page === 1) {
      updateContent(instance.content, html)
    } else {
      appendContent(instance.content, html)
    }

    updateSentinel(instance)
  } catch (error) {
    if (axios.isCancel(error)) return

    logDevError('[Search]', error)

    if (!instance.isClosed && errorHtml && page === 1) updateContent(instance.content, errorHtml)
  }
}

function updateSentinel(instance: SearchInstance): void {
  const existing = instance.content.querySelector<HTMLElement>('.search-sentinel')

  if (!instance.hasMore) {
    existing?.remove()
    return
  }

  if (existing) return

  const sentinel = document.createElement('div')
  sentinel.className = 'search-sentinel'

  instance.content.appendChild(sentinel)

  const scrollRoot = instance.content.closest<HTMLElement>('.search-panel__card') ?? null

  const observer = new IntersectionObserver(
    (entries) => {
      if (!entries[0]?.isIntersecting || instance.isClosed || !instance.hasMore) return

      observer.disconnect()
      sentinel.remove()

      const nextPage = instance.currentPage + 1
      instance.currentPage = nextPage

      handleFetchSearch(instance.currentSearchTerm, instance, false, nextPage)
    },
    { root: scrollRoot, rootMargin: '0px 0px 50px 0px', threshold: 0 },
  )

  observer.observe(sentinel)
}
