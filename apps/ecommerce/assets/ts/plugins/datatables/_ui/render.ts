import type { UnknownRecord } from '@/ts/shared/types'
import { createCell, createRow, createEmptyRow } from '@/ts/shared/elements/table/_ui/elements'

import type { CellRendererRecord, RenderRowsOptions } from '@/ts/plugins/datatables/types'

type RenderDatatableRowsOptions<T = unknown> = {
  cellRenderers?: CellRendererRecord
  rowStyle?: (row: T) => string | undefined
  actionButton?: (row: T) => HTMLTableCellElement
  emptyText?: string
}

/** Renders data rows into a table body element */
function renderRows(
  tbody: HTMLTableSectionElement,
  rows: UnknownRecord[],
  columns: readonly string[],
  options: RenderRowsOptions = {},
): void {
  const frag = document.createDocumentFragment()
  const { cellRenderers = {}, rowStyle, actionButton } = options

  rows.forEach((rowData) => {
    const cells = columns.map((key) => {
      const renderer = cellRenderers[key]
      if (renderer) return createCell(renderer(rowData[key], rowData))
      const val = rowData[key]
      return createCell(val !== null && val !== undefined ? String(val) : '')
    })

    const actionsTd = actionButton ? actionButton(rowData) : null
    const tr = createRow(cells, actionsTd)

    if (typeof rowStyle === 'function') {
      const bg = rowStyle(rowData)
      if (bg) tr.style.backgroundColor = bg
    }

    frag.appendChild(tr)
  })

  tbody.appendChild(frag)
}

/** Render table rows for a DataTable, handling empty state and optional features */
export function renderDatatableRows<T = unknown>(
  tbody: HTMLTableSectionElement,
  rows: T[],
  columns: readonly string[],
  options: RenderDatatableRowsOptions<T> = {},
): void {
  tbody.innerHTML = ''

  const { emptyText = 'No records found' } = options

  const table = tbody.closest('table')
  const wrapper = table?.closest('.dataTable-wrapper')
  const pagination = wrapper?.querySelector<HTMLElement>('.dataTable-pagination')

  if (!rows || rows.length === 0) {
    createEmptyRow(tbody, columns.length + (options.actionButton ? 1 : 0), emptyText)
  } else {
    renderRows(tbody, rows as UnknownRecord[], columns, options as RenderRowsOptions)
  }

  if (pagination) {
    pagination.style.display = !rows || rows.length === 0 ? 'none' : ''
  }
}
