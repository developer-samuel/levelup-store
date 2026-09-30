import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'
import { handleTableDestroy } from '@/ts/shared/elements/table/_handlers/destroyHandler'

export function handleDescriptionClick(e: MouseEvent): void {
  handleTableClick(e, {
    hrefContains: ['/admin/variants/descriptions/edit'],
  })

  handleTableClick(e, {
    selector: '.variant-descriptions-destroy-btn',
    confirmMessage: 'Are you sure you want to delete this description?',
    onClick: (el: HTMLElement) =>
      handleTableDestroy(el, {
        url: '/admin/variants/descriptions/destroy',
        successMessage: 'Description deleted successfully.',
        errorMessage: 'Failed to delete description.',
      }),
  })
}
