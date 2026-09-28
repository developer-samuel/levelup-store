import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

import type { Subtype } from '@/ts/features/admin/products_subtypes/list/types'

const COLUMNS = ['name', 'createdAt'] as const

export function renderSubtypes(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const subtypes = items as Subtype[]
  renderDatatableRows(tbody, subtypes, COLUMNS, {
    emptyText: 'No subtypes found',
  })
}
