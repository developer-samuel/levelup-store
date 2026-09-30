import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { adminListUrl } from '@/ts/features/admin/_utils/api'
import { getLastPathSegment } from '@/ts/features/admin/_utils/url'
import { renderImages } from '@/ts/features/admin/products_variants_images/list/_ui/render'

const TABLE_SELECTOR = '#admin-variant-images-table'
const DATA_KEY = 'images'

export default class AdminImagesTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      adminListUrl('variants/images', getLastPathSegment()),
      renderImages as RenderRowsFn,
      undefined,
      DATA_KEY,
    )
  }
}
