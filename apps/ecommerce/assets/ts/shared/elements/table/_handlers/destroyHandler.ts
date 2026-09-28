import { removeRow } from '@/ts/shared/elements/table/_ui/elements'

import { destroyData } from '@/ts/core/http/_services/destroyData'

import NotyfAlert from '@/ts/plugins/notyf/_components/NotyfAlert'

type DestroyResponse = {
  success?: boolean
  message?: string
}

type TableDestroyOptions = {
  url: string
  idAttr?: string
  successMessage?: string
  errorMessage?: string
}

/**
 * Shared destroy handler for table actions.
 * Handles sending POST requests to destroy endpoints,
 * removing row from table, and showing alerts.
 */
export async function handleTableDestroy(
  el: HTMLElement,
  {
    url,
    idAttr = 'id',
    successMessage = 'Deleted successfully.',
    errorMessage = 'Failed to delete.',
  }: TableDestroyOptions,
): Promise<void> {
  const id = el.dataset.id ?? el.getAttribute(idAttr)
  if (!id) return

  try {
    const data = await destroyData<DestroyResponse>(url, id)

    if (data?.success) {
      removeRow(el)

      NotyfAlert.success(data.message ?? successMessage)
    } else {
      NotyfAlert.error(data?.message ?? errorMessage)
    }
  } catch {
    NotyfAlert.error('Something went wrong. Please try again.')
  }
}
