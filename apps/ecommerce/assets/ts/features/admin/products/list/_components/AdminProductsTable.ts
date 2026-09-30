import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { renderProducts } from '@/ts/features/admin/products/list/_ui/render'
import { handleProductClick } from '@/ts/features/admin/products/list/_handlers/productClickHandler'

const TABLE_SELECTOR = '#admin-products-table'
const URL = '/api/admin/products/list'
const DATA_KEY = 'products'

export default class AdminProductsTable extends BaseDatatable {
  constructor() {
    super(TABLE_SELECTOR, URL, renderProducts as RenderRowsFn, handleProductClick, DATA_KEY)
  }
}
