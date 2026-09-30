import type { AppModule } from '@/ts/app/types'

export const adminVariantDescriptionsModules: AppModule[] = [
  {
    selector: '#admin-variant-descriptions-table',
    module: () => import('@/ts/features/admin/products_variants_descriptions/list/index'),
  },
  {
    selector: '#admin-variant-descriptions-create-form',
    module: () => import('@/ts/features/admin/products_variants_descriptions/create/index'),
  },
  {
    selector: '#admin-variant-descriptions-update-form',
    module: () => import('@/ts/features/admin/products_variants_descriptions/update/index'),
  },
]
