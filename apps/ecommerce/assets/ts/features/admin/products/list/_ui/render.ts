import { createActionButton } from '@/ts/shared/elements/table/_ui/actionButton'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

type Product = {
  id: number
  name: string
  catalogCode: string
  category: string
  type: string
  brand: string
  createdAt: string
  [key: string]: unknown
}

type ProductActionButton = {
  className: string
  text: string
  href: string
}

const COLUMNS = ['name', 'catalogCode', 'category', 'type', 'brand', 'createdAt'] as const

export function renderProducts(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const products = items as Product[]
  renderDatatableRows(tbody, products, COLUMNS, {
    actionButton: (row: Product): HTMLTableCellElement => {
      const td = document.createElement('td')

      const buttons: ProductActionButton[] = [
        {
          className: 'btn btn--sm btn--purple',
          text: 'Subtypes',
          href: `/admin/products/subtypes/${row.id}`,
        },
        {
          className: 'btn btn--sm btn--orange',
          text: 'Variants',
          href: `/admin/variants/${row.id}`,
        },
      ]

      buttons.forEach((btn) => {
        const el = createActionButton({
          className: btn.className,
          text: btn.text,
          id: row.id,
        })

        const a = el.querySelector('a')
        if (a) {
          a.href = btn.href
          td.appendChild(a)
        }
      })

      return td
    },

    emptyText: 'No products found',
  })
}
