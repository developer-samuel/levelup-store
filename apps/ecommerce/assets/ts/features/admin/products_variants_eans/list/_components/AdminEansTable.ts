import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { adminListUrl } from '@/ts/features/admin/_utils/api'
import { getLastPathSegment } from '@/ts/features/admin/_utils/url'
import { renderEans } from '@/ts/features/admin/products_variants_eans/list/_ui/render'
import { handleEanClick } from '@/ts/features/admin/products_variants_eans/list/_handlers/eanClickHandler'

const TABLE_SELECTOR = '#admin-variant-eans-table'
const DATA_KEY = 'eans'

export default class AdminEansTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      adminListUrl('variants/eans', getLastPathSegment()),
      renderEans as RenderRowsFn,
      handleEanClick,
      DATA_KEY,
    )
  }
}
