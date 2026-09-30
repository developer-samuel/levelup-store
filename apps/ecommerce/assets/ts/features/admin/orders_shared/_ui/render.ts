import { createActionButton } from '@/ts/shared/elements/table/_ui/actionButton'

import { renderDatatableRows } from '@/ts/plugins/datatables/_ui/render'

import type { Order, OrderRenderOptions } from '@/ts/features/admin/orders_shared/types'

const COLUMNS = ['code', 'price', 'payment', 'isPaid', 'status', 'createdAt'] as const

export function renderOrders(
  tbody: HTMLTableSectionElement,
  orders: Order[],
  extraOptions: OrderRenderOptions = {},
): void {
  renderDatatableRows(tbody, orders, COLUMNS, {
    cellRenderers: {
      isPaid: (val: unknown) => (val ? '✅' : '❌'),
    },

    ...(extraOptions.rowStyle != null ? { rowStyle: extraOptions.rowStyle } : {}),

    actionButton: (row: Order): HTMLTableCellElement => {
      const td = createActionButton({
        className: 'btn btn--sm btn--green',
        text: 'Show',
        id: row.id,
      })

      const a = td.querySelector('a')
      if (a) a.href = `/admin/orders/show/${row.code}`

      return td
    },

    emptyText: 'No orders found',
  })
}
