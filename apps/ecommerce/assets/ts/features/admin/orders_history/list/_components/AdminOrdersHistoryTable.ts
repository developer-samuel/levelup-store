import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import type { Order } from '@/ts/features/admin/orders_shared/types'
import { ORDERS_DATA_KEY } from '@/ts/features/admin/orders_shared/constants'
import { renderOrders } from '@/ts/features/admin/orders_shared/_ui/render'
import { handleOrderClick } from '@/ts/features/admin/orders_shared/_handlers/orderClickHandler'
import { getHistoryRowStyle } from '@/ts/features/admin/orders_history/list/_utils/historyRowStyle'

const TABLE_SELECTOR = '#admin-orders-history-table'
const URL = '/api/admin/orders/history/list'

export default class AdminOrdersHistoryTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      URL,
      (tbody, items) => renderOrders(tbody, items as Order[], { rowStyle: getHistoryRowStyle }),
      handleOrderClick,
      ORDERS_DATA_KEY,
    )
  }
}
