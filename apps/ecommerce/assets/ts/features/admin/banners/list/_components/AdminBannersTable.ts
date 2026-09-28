import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { renderBanners } from '@/ts/features/admin/banners/list/_ui/render'

const DATA_KEY = 'banners'
const TABLE_SELECTOR = '#admin-banners-table'
const URL = '/api/admin/banners/list'

export default class AdminBannersTable extends BaseDatatable {
  constructor() {
    super(TABLE_SELECTOR, URL, renderBanners, undefined, DATA_KEY)
  }
}
