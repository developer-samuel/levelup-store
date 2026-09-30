import { sleep } from '@/ts/shared/utils/sleep'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

type DestroyHandlerOptions = {
  datasetKey: string
  serviceFn: (id: string) => Promise<void>
  onSuccess: (el: HTMLElement) => void
  logTag: string
  successMessage: string
  confirmMessage?: string
  reloadDelay?: number
}

/** Creates a reusable destroy click handler with confirm dialog, service call, and error handling */
export function handleDestroy(options: DestroyHandlerOptions) {
  return async function (event: Event): Promise<void> {
    event.preventDefault()
    event.stopPropagation()

    if (!(event.currentTarget instanceof HTMLElement)) return

    const el = event.currentTarget

    const id = el.dataset[options.datasetKey]
    if (!id) return

    const confirmed = confirm(options.confirmMessage ?? 'Are you sure you want to delete this item?')
    if (!confirmed) return

    try {
      await options.serviceFn(id)
      NotyfAlert.success(options.successMessage)

      if (options.reloadDelay) {
        await sleep(options.reloadDelay)
        window.location.reload()
      } else {
        options.onSuccess(el)
      }
    } catch {
      NotyfAlert.error('Something went wrong. Please try again.')
    }
  }
}
