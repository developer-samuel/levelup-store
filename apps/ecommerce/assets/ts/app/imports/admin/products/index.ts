import type { AppModule } from '@/ts/app/types'

export const adminProductsModules: AppModule[] = [
  { selector: '#admin-products-table', module: () => import('@/ts/features/admin/products/list/index') },
  {
    selector: '#admin-product-subtypes-table',
    module: () => import('@/ts/features/admin/products_subtypes/list/index'),
  },
]
