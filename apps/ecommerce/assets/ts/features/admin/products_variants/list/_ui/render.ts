import { createActionButton } from '@/ts/shared/elements/table/_ui/actionButton'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

import type { RendererActionButton } from '@/ts/features/admin/products_variants/types'

type Variant = {
  id: number
  sku: string
  name: string
  price: number | string
  discountedPrice: number | string
  status: string
  createdAt: string
  [key: string]: unknown
}

const COLUMNS = ['sku', 'name', 'price', 'discountedPrice', 'status', 'createdAt'] as const

export function renderVariants(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const variants = items as Variant[]
  renderDatatableRows(tbody, variants, COLUMNS, {
    cellRenderers: {
      status: (val: unknown) => (typeof val === 'string' && val.toLowerCase() === 'available' ? '✅' : '❌'),
    },

    actionButton: (row: Variant): HTMLTableCellElement => {
      const td = document.createElement('td')

      const buttons: RendererActionButton[] = [
        {
          className: 'btn btn--sm btn--orange',
          text: 'Eans',
          href: `/admin/variants/eans/${row.id}`,
          attrs: {},
        },
        {
          className: 'btn btn--sm btn--blue',
          text: 'Images',
          href: `/admin/variants/images/${row.id}`,
          attrs: {},
        },
        {
          className: 'btn btn--sm btn--purple',
          text: 'Descriptions',
          href: `/admin/variants/descriptions/${row.id}`,
          attrs: {},
        },
      ]

      buttons.forEach((btn) => {
        const el = createActionButton({ className: btn.className, text: btn.text, id: row.id })
        const a = el.querySelector('a')
        if (a) {
          a.href = btn.href
          td.appendChild(a)
        }
      })

      return td
    },

    emptyText: 'No variants found',
  })
}
