import DataTable from 'vanilla-datatables'

import 'vanilla-datatables/dist/vanilla-dataTables.min.css'

import { logDevError } from '@/ts/shared/utils/logger'

import type { RenderRowsFn, ClickHandlerFn } from '@/ts/plugins/datatables/types'
import { LOADING_HTML } from '@/ts/plugins/datatables/constants'
import { DATATABLES_CONFIG } from '@/ts/plugins/datatables/config'
import { showNoRecordsFallback } from '@/ts/plugins/datatables/_ui/fallbacks'
import { loadAndRenderRows } from '@/ts/plugins/datatables/_interactions/loadAndRender'
import { enhanceWrapper } from '@/ts/plugins/datatables/_interactions/enhanceWrapper'

/** Initialize a DataTable: fetch data, attach listeners, apply enhancements, handle errors */
export async function setupDatatable(
  table: HTMLTableElement,
  url: string,
  renderRows: RenderRowsFn,
  clickHandler?: ClickHandlerFn,
  dataKey?: string,
): Promise<DataTable | null> {
  if (!table) return null

  try {
    const tbody = table.querySelector('tbody') ?? table.createTBody()
    tbody.innerHTML = LOADING_HTML

    await loadAndRenderRows(table, url, renderRows, clickHandler, dataKey)

    const dt = new DataTable(table, DATATABLES_CONFIG)
    enhanceWrapper(table)

    return dt
  } catch (error) {
    logDevError('[Datatable]', error)
    showNoRecordsFallback(table)

    return null
  }
}
