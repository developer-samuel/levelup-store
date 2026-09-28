import type { DataTableConfig } from '@/ts/plugins/datatables/types'

export const DATATABLES_CONFIG: DataTableConfig = {
  perPage: 10,
  perPageSelect: [10, 25, 50, 100],
  sortable: true,
  searchable: true,
  nextPrev: true,
  firstLast: true,
  paginationLength: 3,
} as const
