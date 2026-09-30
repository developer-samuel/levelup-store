import { renderActionButtons } from '@/ts/shared/elements/table/_ui/actionButton'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

type Description = {
  id: number
  variantId: number
  title: string
  body: string
  createdAt: string
  [key: string]: unknown
}

const COLUMNS = ['title', 'body', 'createdAt'] as const

export function renderDescriptions(tbody: HTMLTableSectionElement, items: unknown[]): void {
  const descriptions = items as Description[]
  renderDatatableRows(tbody, descriptions, COLUMNS, {
    actionButton: (row: Description): HTMLTableCellElement => {
      const td = document.createElement('td')

      renderActionButtons(
        td,
        [
          {
            className: 'btn btn--sm btn--blue variant-descriptions-update-btn',
            text: 'Edit',
            href: `/admin/variants/descriptions/edit/${row.variantId}/${row.id}`,
            attrs: {},
          },
          {
            className: 'btn btn--sm btn--red variant-descriptions-destroy-btn',
            text: 'Destroy',
            href: '#',
            attrs: {
              'data-id': String(row.id),
              role: 'button',
            },
          },
        ],
        row.id,
      )

      return td
    },

    emptyText: 'No descriptions found',
  })
}
