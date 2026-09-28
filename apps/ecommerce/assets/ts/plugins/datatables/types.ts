import type { UnknownRecord } from '@/ts/shared/types'

export type DataTableConfig = {
  perPage: number
  perPageSelect: readonly number[]
  sortable: boolean
  searchable: boolean
  nextPrev: boolean
  firstLast: boolean
  paginationLength: number
}

export type CellRenderer = (value: unknown, row: UnknownRecord) => string | HTMLElement
export type CellRendererRecord = Record<string, CellRenderer>

export type RenderRowsOptions = {
  cellRenderers?: CellRendererRecord
  rowStyle?: (row: UnknownRecord) => string | null | undefined
  actionButton?: (row: UnknownRecord) => HTMLTableCellElement | null
}

export type RenderRowsFn = (tbody: HTMLTableSectionElement, items: unknown[]) => void
export type ClickHandlerFn = (event: MouseEvent) => void
