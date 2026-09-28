import type { StringRecord } from '@/ts/shared/types'
import { createActionButton } from '@/ts/shared/elements/table/_ui/actionButton'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

type Brand = {
  id: number
  name: string
  createdAt: string
  [key: string]: unknown
}

type ActionButtonConfig = {
  className: string
  text: string
  href?: string
  attrs: StringRecord
}

const COLUMNS = ['name', 'createdAt'] as const

function getBrandButtons(id: number): ActionButtonConfig[] {
  return [
    {
      className: 'btn btn--sm btn--blue brand-update-btn',
      text: 'Edit',
      href: `/admin/brands/edit/${id}`,
      attrs: {},
    },
    {
      className: 'btn btn--sm btn--red brand-destroy-btn',
      text: 'Destroy',
      attrs: {
        'data-id': String(id),
        role: 'button',
      },
    },
  ]
}

function createBrandActionCell(row: Brand): HTMLTableCellElement {
  const td = document.createElement('td')

  getBrandButtons(row.id).forEach((btn) => {
    const el = createActionButton({ className: btn.className, text: btn.text, id: row.id })
    const a = el.querySelector('a')
    if (a) {
      if (btn.href !== undefined) a.href = btn.href
      Object.entries(btn.attrs).forEach(([k, v]) => a.setAttribute(k, v))
      td.appendChild(a)
    }
  })

  return td
}

export function renderBrands(tbody: HTMLTableSectionElement, items: unknown[]): void {
  renderDatatableRows(tbody, items as Brand[], COLUMNS, {
    actionButton: createBrandActionCell,
    emptyText: 'No brands found',
  })
}
