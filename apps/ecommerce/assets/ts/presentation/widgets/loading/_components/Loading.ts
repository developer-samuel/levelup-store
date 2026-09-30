import { LOADING_SHOW, LOADING_HIDE } from '@/ts/shared/events/loading'

import type { LoadingInstance } from '@/ts/presentation/widgets/loading/types'
import { showLoading, hideLoading } from '@/ts/presentation/widgets/loading/_ui/loadingAction'
import { setupLoaderOnReady } from '@/ts/presentation/widgets/loading/_interactions/setupLoaderOnReady'

export class Loading implements LoadingInstance {
  readonly selector: string
  readonly hiddenClass: string
  readonly delay: number
  element: HTMLElement | null = null
  cancelHide: (() => void) | null = null

  constructor(selector: string, hiddenClass: string, delay: number) {
    this.selector = selector
    this.hiddenClass = hiddenClass
    this.delay = delay

    this.init()
  }

  private init(): void {
    document.addEventListener(LOADING_SHOW, showLoading)
    document.addEventListener(LOADING_HIDE, hideLoading)

    setupLoaderOnReady(this)
  }
}
