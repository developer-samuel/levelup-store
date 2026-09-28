import { query } from '@/ts/shared/utils/dom/query'

import type { LoadingInstance } from '@/ts/presentation/widgets/loading/types'
import { startHideLoader } from '@/ts/presentation/widgets/loading/_interactions/startHideLoader'

export function setupLoader(instance: LoadingInstance): void {
  instance.element = query<HTMLElement>(instance.selector)

  if (!instance.element) {
    return
  }

  if (document.readyState === 'complete') {
    startHideLoader(instance)
  } else {
    window.addEventListener('load', () => startHideLoader(instance))
  }

  window.addEventListener('pageshow', (e: PageTransitionEvent) => {
    if (e.persisted) startHideLoader(instance)
  })
}
