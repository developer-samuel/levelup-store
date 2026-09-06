import { createTableImage } from '@/ts/shared/elements/table/_ui/image'
import { storageUrl } from '@/ts/core/storage/storageUrl'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

type VariantImage = {
  id: number
  variantId: number
  path: string
  createdAt: string
  [key: string]: unknown
}

const COLUMNS = ['path', 'createdAt'] as const

export function renderImages(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const images = items as VariantImage[]
  renderDatatableRows(tbody, images, COLUMNS, {
    cellRenderers: {
      path: (val: unknown) => createTableImage(storageUrl(String(val ?? ''))),
    },
    emptyText: 'No images found',
  })
}
