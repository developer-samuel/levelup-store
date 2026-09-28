import type { AppModule } from '@/ts/app/types'

export const adminVariantMainModules: AppModule[] = [
  { selector: '#admin-variants-table', module: () => import('@/ts/features/admin/products_variants/list/index') },
]
