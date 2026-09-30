import type { AppModule } from '@/ts/app/types'

import { adminVariantMainModules } from '@/ts/app/imports/admin/products_variants/modules/_main'
import { adminVariantEansModules } from '@/ts/app/imports/admin/products_variants/modules/_eans'
import { adminVariantDescriptionsModules } from '@/ts/app/imports/admin/products_variants/modules/_descriptions'
import { adminVariantImagesModules } from '@/ts/app/imports/admin/products_variants/modules/_images'

export const adminProductsVariantsModules: AppModule[] = [
  ...adminVariantMainModules,
  ...adminVariantEansModules,
  ...adminVariantDescriptionsModules,
  ...adminVariantImagesModules,
]
