import { showTableFallback } from '@/ts/plugins/datatables/_ui/fallbacks'
import { limitPaginationWindow } from '@/ts/plugins/datatables/_ui/pagination'
import { observeDatatablePagination } from '@/ts/plugins/datatables/_observers/paginationObserver'

/** Enhance a DataTable with standard UI behaviors like pagination limits and observation */
export function applyDatatableEnhancements(table: HTMLTableElement): void {
  if (!table) return

  try {
    limitPaginationWindow(table)

    observeDatatablePagination(table)
  } catch {
    showTableFallback(table)
  }
}
