import type { RenderRowsFn, ClickHandlerFn } from '@/ts/plugins/datatables/types'
import { LOADING_HTML } from '@/ts/plugins/datatables/constants'
import { fetchDatatableData } from '@/ts/plugins/datatables/_services/datatablesService'

/** Load DataTable data, render rows, and attach row click listeners */
export async function loadAndRenderRows(
  table: HTMLTableElement,
  url: string,
  renderRows: RenderRowsFn,
  clickHandler?: ClickHandlerFn,
  dataKey?: string,
): Promise<void> {
  if (!table) return

  const tbody = table.querySelector('tbody') ?? table.createTBody()
  tbody.innerHTML = LOADING_HTML

  const items = await fetchDatatableData(url, dataKey !== undefined ? { dataKey } : {})
  if (!items) {
    tbody.innerHTML = ''
    return
  }
  renderRows(tbody, items)

  if (clickHandler) {
    tbody.removeEventListener('click', clickHandler)
    tbody.addEventListener('click', clickHandler)
  }
}
