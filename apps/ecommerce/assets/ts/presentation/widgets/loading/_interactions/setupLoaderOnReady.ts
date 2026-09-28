import type { LoadingInstance } from '@/ts/presentation/widgets/loading/types'
import { setupLoader } from '@/ts/presentation/widgets/loading/_interactions/setupLoader'

export function setupLoaderOnReady(instance: LoadingInstance): void {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => setupLoader(instance))
  } else {
    setupLoader(instance)
  }
}
