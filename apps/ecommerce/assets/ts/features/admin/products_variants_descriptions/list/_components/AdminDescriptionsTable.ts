import type { RenderRowsFn } from '@/ts/plugins/datatables/types'
import BaseDatatable from '@/ts/plugins/datatables/_abstracts/BaseDatatable'

import { adminListUrl } from '@/ts/features/admin/_utils/api'
import { getLastPathSegment } from '@/ts/features/admin/_utils/url'
import { renderDescriptions } from '@/ts/features/admin/products_variants_descriptions/list/_ui/render'
import { handleDescriptionClick } from '@/ts/features/admin/products_variants_descriptions/list/_handlers/descriptionClickHandler'

const TABLE_SELECTOR = '#admin-variant-descriptions-table'
const DATA_KEY = 'descriptions'

export default class AdminDescriptionsTable extends BaseDatatable {
  constructor() {
    super(
      TABLE_SELECTOR,
      adminListUrl('variants/descriptions', getLastPathSegment()),
      renderDescriptions as RenderRowsFn,
      handleDescriptionClick,
      DATA_KEY,
    )
  }
}
