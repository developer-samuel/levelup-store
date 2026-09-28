import type { AppModule } from '@/ts/app/types'

export const ordersModules: AppModule[] = [
  { selector: '.order', module: () => import('@/ts/features/orders/create/index') },
  {
    selector: '.orders[data-mercure-hub]',
    module: () => import('@/ts/features/orders/_realtime/statuses'),
  },
  {
    selector: '.orders__card-detail[data-mercure-hub]',
    module: () => import('@/ts/features/orders/_realtime/status'),
  },
]
