import { handleTableClick } from '@/ts/shared/elements/table/_handlers/clickHandler'
import { handleTableDestroy } from '@/ts/shared/elements/table/_handlers/destroyHandler'

export function handleBrandClick(e: Event): void {
  handleTableClick(e as MouseEvent, {
    hrefContains: ['/admin/brands/edit'],
  })

  handleTableClick(e as MouseEvent, {
    selector: '.brand-destroy-btn',
    confirmMessage: 'Are you sure you want to delete this brand?',
    onClick: (el: HTMLElement) =>
      handleTableDestroy(el, {
        url: '/admin/brands/destroy',
        successMessage: 'Brand deleted successfully.',
        errorMessage: 'Failed to delete brand.',
      }),
  })
}
