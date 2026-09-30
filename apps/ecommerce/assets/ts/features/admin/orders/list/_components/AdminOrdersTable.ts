import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { ORDERS_DATA_KEY } from '@/ts/features/admin/orders_shared/constants'
import { renderOrders } from '@/ts/features/admin/orders_shared/_ui/render'
import { handleOrderClick } from '@/ts/features/admin/orders_shared/_handlers/orderClickHandler'

const TABLE_SELECTOR = '#admin-orders-table'
const URL = '/api/admin/orders/list'

export default class AdminOrdersTable extends BaseDatatable {
  constructor() {
    super(TABLE_SELECTOR, URL, renderOrders as RenderRowsFn, handleOrderClick, ORDERS_DATA_KEY)
  }
}
