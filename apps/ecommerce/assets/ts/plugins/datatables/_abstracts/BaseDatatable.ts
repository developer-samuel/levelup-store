import type DataTable from 'vanilla-datatables'

import { query } from '@/ts/shared/utils/dom/query'

import type { RenderRowsFn, ClickHandlerFn } from '@/ts/plugins/datatables/types'
import { applyDatatableEnhancements } from '@/ts/plugins/datatables/_interactions/applyEnhancements'
import { setupDatatable } from '@/ts/plugins/datatables/_interactions/setupDatatable'

export default abstract class BaseDatatable {
  protected readonly table: HTMLTableElement
  protected readonly url: string
  protected readonly renderRows: RenderRowsFn
  protected readonly clickHandler?: ClickHandlerFn | undefined
  protected readonly dataKey?: string | undefined
  protected readonly tbody: HTMLTableSectionElement
  protected dt: DataTable | null = null

  constructor(
    tableSelector: string,
    url: string,
    renderRows: RenderRowsFn,
    clickHandler?: ClickHandlerFn,
    dataKey?: string,
  ) {
    const table = query<HTMLTableElement>(tableSelector)
    if (!table) {
      throw new Error(`BaseDatatable: table "${tableSelector}" not found.`)
    }

    this.table = table
    this.url = url
    this.renderRows = renderRows
    this.clickHandler = clickHandler
    this.dataKey = dataKey
    this.tbody = table.querySelector('tbody') ?? table.createTBody()

    void this.init()
  }

  protected async init(): Promise<void> {
    this.dt = await setupDatatable(this.table, this.url, this.renderRows, this.clickHandler, this.dataKey)

    applyDatatableEnhancements(this.table)
  }
}
