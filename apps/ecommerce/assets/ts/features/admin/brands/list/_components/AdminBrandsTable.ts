import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { renderBrands } from '@/ts/features/admin/brands/list/_ui/render'
import { handleBrandClick } from '@/ts/features/admin/brands/list/_handlers/brandClickHandler'

const DATA_KEY = 'brands'
const TABLE_SELECTOR = '#admin-brands-table'
const URL = '/api/admin/brands/list'

export default class AdminBrandsTable extends BaseDatatable {
  constructor() {
    super(TABLE_SELECTOR, URL, renderBrands, handleBrandClick, DATA_KEY)
  }
}
