import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { adminListUrl } from '@/ts/features/admin/_utils/api'
import { getLastPathSegment } from '@/ts/features/admin/_utils/url'
import { renderSubtypes } from '@/ts/features/admin/products_subtypes/list/_ui/render'

const TABLE_SELECTOR = '#admin-product-subtypes-table'
const DATA_KEY = 'subtypes'

export default class AdminSubtypesTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      adminListUrl('products/subtypes', getLastPathSegment()),
      renderSubtypes as RenderRowsFn,
      undefined,
      DATA_KEY,
    )
  }
}
