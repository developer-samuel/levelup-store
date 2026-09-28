import type { AppModule } from '@/ts/app/types'

export const adminBannersModules: AppModule[] = [
  { selector: '#admin-banners-table', module: () => import('@/ts/features/admin/banners/list/index') },
]
