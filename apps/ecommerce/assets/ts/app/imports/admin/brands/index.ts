import type { AppModule } from '@/ts/app/types'

export const adminBrandsModules: AppModule[] = [
  { selector: '#admin-brands-table', module: () => import('@/ts/features/admin/brands/list/index') },
  { selector: '#admin-brands-create-form', module: () => import('@/ts/features/admin/brands/create/index') },
  { selector: '#admin-brands-update-form', module: () => import('@/ts/features/admin/brands/update/index') },
]
