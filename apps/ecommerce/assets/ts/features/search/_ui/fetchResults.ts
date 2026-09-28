import axios from 'axios'

import { logDevError } from '@/ts/shared/utils/logger'

import type { SearchInstance, RenderFetchOptions } from '@/ts/features/search/types'
import { updateContent } from '@/ts/features/search/_ui/content'
import { show } from '@/ts/features/search/_ui/visibility'

type NullableString = Promise<string | null | undefined>

export async function renderFetchResults(
  instance: SearchInstance,
  fetchFn: (signal?: AbortSignal) => NullableString,
  { loadingHtml, noResultsHtml, errorHtml, signal, showPanel = true }: RenderFetchOptions,
): Promise<void> {
  instance.isClosed = false

  if (showPanel) {
    updateContent(instance.content, loadingHtml)
    show(instance.panel)
  }

  try {
    const html = await fetchFn(signal)
    if (!instance.isClosed) {
      updateContent(instance.content, html != null && html.trim() !== '' ? html.trim() : noResultsHtml)
    }
  } catch (error) {
    if (axios.isCancel(error)) return

    logDevError('[Search]', error)

    if (!instance.isClosed && errorHtml) updateContent(instance.content, errorHtml)
  }
}
