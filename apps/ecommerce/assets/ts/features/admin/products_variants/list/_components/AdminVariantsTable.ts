import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { adminListUrl } from '@/ts/features/admin/_utils/api'
import { getLastPathSegment } from '@/ts/features/admin/_utils/url'
import { renderVariants } from '@/ts/features/admin/products_variants/list/_ui/render'
import { handleVariantClick } from '@/ts/features/admin/products_variants/list/_handlers/variantClickHandler'

const TABLE_SELECTOR = '#admin-variants-table'
const DATA_KEY = 'variants'

export default class AdminVariantsTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      adminListUrl('variants', getLastPathSegment()),
      renderVariants as RenderRowsFn,
      handleVariantClick,
      DATA_KEY,
    )
  }
}
