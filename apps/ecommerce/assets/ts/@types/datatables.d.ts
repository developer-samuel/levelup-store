declare module 'vanilla-datatables' {
  export type DataTableOptions = {
    perPage?: number
    sortable?: boolean
    searchable?: boolean
    fixedHeight?: boolean
    fixedColumns?: boolean
    layout?: { top?: string; bottom?: string }
    labels?: { placeholder?: string; perPage?: string; noRows?: string; info?: string }
    [key: string]: unknown
  }

  export default class DataTable {
    constructor(table: HTMLTableElement, options?: DataTableOptions)

    destroy(): void
    refresh(): void
    update(): void
    extend?(name: string, fn: (...args: unknown[]) => unknown): void
  }
}
