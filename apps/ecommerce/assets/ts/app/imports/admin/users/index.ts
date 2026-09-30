import type { AppModule } from '@/ts/app/types'

export const adminUsersModules: AppModule[] = [
  { selector: '#admin-users-table', module: () => import('@/ts/features/admin/users/list/index') },
]
