import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'
import { handleTableDestroy } from '@/ts/shared/elements/table/_handlers/destroyHandler'

export function handleEanClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/variants/eans/edit'],
  })

  handleTableClick(e, {
    selector: '.variant-eans-destroy-btn',
    confirmMessage: 'Are you sure you want to delete this ean?',
    onClick: (el: HTMLElement) =>
      handleTableDestroy(el, {
        url: '/admin/variants/eans/destroy',
        successMessage: 'EAN deleted successfully.',
        errorMessage: 'Failed to delete EAN.',
      }),
  })
}
