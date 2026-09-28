import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

type Banner = {
  name: string
  isActive: boolean
  createdAt: string
  [key: string]: unknown
}

const COLUMNS = ['name', 'isActive', 'createdAt'] as const

export function renderBanners(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const banners = items as Banner[]

  renderDatatableRows(tbody, banners, COLUMNS, {
    cellRenderers: {
      isActive: (val: unknown) => (val ? '✅' : '❌'),
    },
    emptyText: 'No banners found',
  })
}
