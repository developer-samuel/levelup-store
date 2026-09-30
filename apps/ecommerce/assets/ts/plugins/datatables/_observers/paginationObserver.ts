import { limitPaginationWindow } from '@/ts/plugins/datatables/_ui/pagination'

/** Observes changes in a DataTable pagination container and enforces pagination limits */
export function observeDatatablePagination(table: HTMLTableElement): void {
  const wrapper = table.closest('.dataTable-wrapper')
  if (!wrapper) return

  const paginationContainer = wrapper.querySelector('.dataTable-pagination')
  if (!paginationContainer) return

  const observer = new MutationObserver(() => limitPaginationWindow(table))

  observer.observe(paginationContainer, { childList: true, subtree: true })
}
