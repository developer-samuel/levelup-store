import type { AppModule } from '@/ts/app/types'

export const adminOrdersModules: AppModule[] = [
  { selector: '#admin-orders-table', module: () => import('@/ts/features/admin/orders/list/index') },
  { selector: '#admin-orders-status-form', module: () => import('@/ts/features/admin/orders/status/index') },
  { selector: '#admin-orders-history-table', module: () => import('@/ts/features/admin/orders_history/list/index') },
]
