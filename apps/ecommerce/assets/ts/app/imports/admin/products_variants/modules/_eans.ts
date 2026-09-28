import type { AppModule } from '@/ts/app/types'

export const adminVariantEansModules: AppModule[] = [
  {
    selector: '#admin-variant-eans-table',
    module: () => import('@/ts/features/admin/products_variants_eans/list/index'),
  },
  {
    selector: '#admin-variant-eans-create-form',
    module: () => import('@/ts/features/admin/products_variants_eans/create/index'),
  },
  {
    selector: '#admin-variant-eans-update-form',
    module: () => import('@/ts/features/admin/products_variants_eans/update/index'),
  },
]
