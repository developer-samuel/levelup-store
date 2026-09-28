import type { AppModule } from '@/ts/app/types'

export const adminDashboardModules: AppModule[] = [
  { selector: '#admin-dashboard-page', module: () => import('@/ts/features/admin/dashboard/index') },
]
